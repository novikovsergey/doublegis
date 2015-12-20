<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\helpers\ArrayHelper;
use app\models\Building;
use app\models\Company;
use app\models\CompanyPhone;
use app\models\CompanyRubric;
use app\models\Rubric;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GeneratorController extends Controller
{
	const MIN_RUBRIC_FOR_COMPANY = 1;
	const MAX_RUBRIC_FOR_COMPANY = 3;

	/**
	 * @var \Faker\Generator
	 */
	private $faker;

	public function init()
	{
		$this->faker = \Faker\Factory::create('ru_RU');
	}

	public function actionGenerate()
	{
		$this->contructBuildings();
		$this->openCompanies(1000);
		$this->providePhones(3000);
		$this->createRubrics();
		$this->distributeRubrics();
	}

	private function contructBuildings($count = 100)
	{

		print_r("Construct buildings. Count: " . $count ."...");
		for ($i = 0; $i < $count; $i++) {
			$building = new Building();

			$building->address = $this->faker->address;
			$building->location = Building::getPostgisFormatByCoordinate($this->faker->longitude, $this->faker->latitude);

			$building->save();

		}
		print_r("DONE". PHP_EOL);

	}

	private function openCompanies($count = 100)
	{
		print_r("Open companies. Count: " . $count ."...");
		$buildings = Building::find()->asArray()->all();
		$building_ids = ArrayHelper::getColumn($buildings, 'id');

		$building_ids_length = count($building_ids) - 1;
		for ($i = 0; $i < $count; $i++) {
			$company = new Company();
			$company->title = $this->faker->unique()->company;
			$company->building_id = $building_ids[mt_rand(1, $building_ids_length)];

			$company->save();
		}
		print_r("DONE". PHP_EOL);
	}

	private function providePhones($count = 150)
	{
		print_r("Provide phones. Count: " . $count ."...");
		$companies = Company::find()->asArray()->all();
		$company_ids = ArrayHelper::getColumn($companies, 'id');

		$company_ids_length = count($company_ids) - 1;

		for ($i = 0; $i < $count; $i++) {
			$company_phone = new CompanyPhone();
			$company_phone->phone = $this->faker->unique()->phoneNumber;
			$company_phone->company_id = $company_ids[mt_rand(1, $company_ids_length)];

			$company_phone->save();

		}

		print_r("DONE". PHP_EOL);
	}

	private function createRubrics($count = 50)
	{
		print_r("Create rubrics. Count: " . $count ."...");
		for ($i = 1; $i <= $count; $i++) {
			$rubric = new Rubric();
			$rubric->title = $this->faker->unique()->word;
			if ($i === 1) {
				$parent_id = NULL;
			} else {
				do {
					$parent_id = $this->faker->optional(0.7)->numberBetween(1, $i);
				} while ($parent_id === $i);
			}
			$rubric->parent_id = $parent_id;

			$rubric->save();

		}
		print_r("DONE". PHP_EOL);
	}
	/** @todo Послу уточннеия задания модифицировать метод  */
	private function distributeRubrics()
	{

		print_r("Distribute rubrics for company...");

		$companies = Company::find()->all();
		$rubrics = Rubric::find()->asArray()->all();
		$rubrics_ids = ArrayHelper::getColumn($rubrics, 'id');
		$rubrics_ids_count = count($rubrics_ids);

		foreach ($companies as $company) {
			$this->faker->unique(true);
			$rubric_for_company_count = $this->faker->numberBetween(self::MIN_RUBRIC_FOR_COMPANY, self::MAX_RUBRIC_FOR_COMPANY);
			for ($i = 1; $i <= $rubric_for_company_count; $i++) {


				$company_rubric = new CompanyRubric();
				$company_rubric->company_id = $company->id;
				$company_rubric->rubric_id = $rubrics_ids[$this->faker->unique()->numberBetween(0, $rubrics_ids_count - 1)];
				$company_rubric->save();
			}

		}
		print_r("DONE" . PHP_EOL);
	}


}
