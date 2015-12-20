<?php

namespace app\controllers;

use app\models\Building;
use app\models\CompanyPhone;
use Faker\Provider\fr_BE\Company;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

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

		$building_id = (int) \Yii::$app->request->get('building_id');
		if($building_id) $query->andWhere(["company.building_id" => $building_id]);


//		$rubric_ids = $this->getIds('rubric_ids');
//		$company_ids = $this->getIds('ids');

		$get_radius = \Yii::$app->request->get('radius');
		$get_envelope = \Yii::$app->request->get('envelope');

			if($get_radius && !$get_envelope) {
			$location = StringHelper::explode($get_radius, ',');
			$query->andWhere("ST_DWithin(building.location, (ST_SetSRID(ST_MakePoint(".$location[0].",".$location[1]."), 4326)), ".$location[2].")");
		}

		if($get_envelope && !$get_radius) {
			$location = StringHelper::explode($get_envelope, ',');
			$query->andWhere("location && ST_MakeEnvelope(".$location[0].", ".$location[1].", ".$location[2].", ".$location[3].", 4326)");
		}
//		$get_search_query =\Yii::$app->request->get('q');

		$companies = $query->createCommand()->queryAll();

		foreach($companies as $key => $company) {
			$companies[$key]['phones'] = ArrayHelper::getColumn(CompanyPhone::find()->asArray()->where(["company_id" => $company["id"]])->all(), 'phone');
		}
		return $companies;

	}

	public function actionRubrics()
	{
		$rubric_ids = $this->getIds('ids');

	}

	private function getIds($get_param_name)
	{
		$get_ids = \Yii::$app->request->get($get_param_name);
		$ids = StringHelper::explode($get_ids, ',');

		foreach ($ids as $key => $id) {
			$ids[$key] = (int)$id;
		}

		return $ids;
	}


}
