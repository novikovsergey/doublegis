<?php

namespace app\controllers;

use app\models\Building;
use app\models\CompanyPhone;
use app\models\CompanyRubric;
use app\models\Rubric;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use \yii\helpers\VarDumper;

class MainController extends \yii\web\Controller
{
	public function init()
	{
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	}

	public function actionBuildings()
	{
		$buildings = Building::find()->select(['id', 'address', "ST_X(location)||','||ST_Y(location) as location"])->asArray()->all();
		return $buildings;
	}

	public function actionCompanies()
	{
		$query = new \yii\db\Query;

		$query->select(['company.id', 'company.title', 'building.address'])
			->from('company')
			->join('INNER JOIN', 'building', 'company.building_id = building.id');

		$this->filterBuilding($query);

		$this->filterRubrics($query);

		$this->filterCompanies($query);

		$this->filterGeometry($query);

		$this->titleSearch($query);

		$query->orderBy('id');
		$query->limit(1000);

		$companies = $query->createCommand()->queryAll();

		// заполненией найденных компаний, рубриками и телефонами
		foreach ($companies as $key => $company) {
			$companies[$key]['phones'] = ArrayHelper::getColumn(CompanyPhone::find()->asArray()->where(["company_id" => $company["id"]])->all(), 'phone');
			$companies[$key]['rubrics'] = Rubric::find()
				->select(['rubric.id', 'rubric.title'])
				->asArray()
				->innerJoin('company_rubric', 'company_rubric.rubric_id = rubric.id')
				->where(['company_rubric.company_id' => $company["id"]])
				->all();
		}
		return $companies;

	}

	private function filterBuilding(\yii\db\Query $query)
	{
		$get_building_id = \Yii::$app->request->get('building_id');

		if (!empty($get_building_id)) {
			if ((int)$get_building_id) {
				$building_id = (int)$get_building_id;
			} else {
				throw new \yii\web\BadRequestHttpException;
			}
		}

		if (isset($building_id)) $query->andWhere(["company.building_id" => $building_id]);
	}

	private function filterRubrics(\yii\db\Query $query)
	{
		$rubric_ids = $this->getIds('rubric_ids');
		if ($rubric_ids) {
			$rubrics = Rubric::findRecursive($rubric_ids, 'down');
			$rubric_ids = array_merge($rubric_ids, ArrayHelper::getColumn($rubrics,'id'));
			$sub_query = CompanyRubric::find()->select('company_rubric.company_id')->where(['company_rubric.rubric_id' => $rubric_ids]);
			$query->andWhere(['in', 'company.id', $sub_query]);
		}
	}

	private function filterCompanies(\yii\db\Query $query)
	{
		$company_ids = $this->getIds('ids');
		if ($company_ids) {
			$query->andWhere(['company.id' => $company_ids]);
		}
	}

	private function filterGeometry(\yii\db\Query $query)
	{
		$get_radius = \Yii::$app->request->get('radius');
		$get_envelope = \Yii::$app->request->get('envelope');

		if ($get_radius && $get_envelope) throw new \yii\web\BadRequestHttpException;

		// поиск всех организаций в радиусе от указаной точке
		if ($get_radius && !$get_envelope) {

			if (!preg_match('/^(\-?\d+(\.\d+)?),(\-?\d+(\.\d+)?),(\d+(\.\d+)?)$/', $get_radius)) {
				throw new \yii\web\BadRequestHttpException;
			}

			$location = StringHelper::explode($get_radius, ',');
			$query->andWhere("ST_DWithin(building.location, (ST_SetSRID(ST_MakePoint(:x_coordinate, :y_coordinate), 4326)), :radius)", [
				":x_coordinate" => $location[0],
				":y_coordinate" => $location[1],
				":radius" => $location[2],
			]);
		}

		// поиск по четырёхугольнику
		if ($get_envelope && !$get_radius) {

			if (!preg_match('/^(\-?\d+(\.\d+)?),(\-?\d+(\.\d+)?),(\-?\d+(\.\d+)?),(\-?\d+(\.\d+)?)$/', $get_envelope)) {
				throw new \yii\web\BadRequestHttpException;
			}

			$location = StringHelper::explode($get_envelope, ',');
			$query->andWhere("location && ST_MakeEnvelope(:x_min_coordinate, :y_min_coordinate, :x_max_coordinate, :y_max_coordinate, 4326)", [
				":x_min_coordinate" => $location[0],
				":y_min_coordinate" => $location[1],
				":x_max_coordinate" => $location[2],
				":y_max_coordinate" => $location[3],
			]);
		}
	}

	private function titleSearch(\yii\db\Query $query)
	{
		$get_search_query = \Yii::$app->request->getQueryParam('q');
		if ($get_search_query) {
			$query->andWhere("company.title ILIKE '%'||:search_query||'%'", [':search_query' => $get_search_query]);
		}
	}

	public function actionRubrics()
	{
		$rubric_ids = $this->getIds('ids');
		$rubrics = Rubric::findRecursive($rubric_ids);
		if(!empty($rubrics)) {
			$rubrics = Rubric::buildTree($rubrics);
		}
		return $rubrics;
	}

	private function getIds($get_param_name)
	{
		$ids = null;

		$get_ids = \Yii::$app->request->getQueryParam($get_param_name);

		if (!empty($get_ids)) {
			$ids = StringHelper::explode($get_ids, ',');

			foreach ($ids as $key => $id) {
				if (!(int)$id) throw new \yii\web\BadRequestHttpException;
				$ids[$key] = (int)$id;
			}
		}
		return $ids;
	}
}