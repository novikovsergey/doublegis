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
    /**
     * @var \Faker\Generator
     */
    private $faker;

    public function init(){
        $this->faker =  \Faker\Factory::create('ru_RU');
    }
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        $min_length = 5;
        $max_length = 10;
        $count = 10;

        $alphabet = array('euioa', 'rtpsdfghklzvbnm');
        $alphabet_length = array(mb_strlen($alphabet[0]), mb_strlen($alphabet[1]));
        $result = array();

        for ($j = 0; $j < $count; ++$j) {
            $word_length = mt_rand($min_length, $max_length);
            $word = '';
            $char_type = 0;
            for ($i = 0; $i < $word_length; ++$i) {
                if ($char_type === 0) $char_type = 1;
                else $char_type = mt_rand(0, 1);

                do {
                    $s = $alphabet[$char_type][mt_rand(0, $alphabet_length[$char_type] - 1)];
                } while ($i != 0 && $s == $word[mb_strlen($word) - 1]);
                $word .= $s;
            }
            $result[] = $word;
        }

        print_r($result);
    }

    public function actionGenerate(){
        $this->contructBuildings();
        $this->openCompanies();
        $this->providePhones();
    }

    private function contructBuildings($count = 100){
        for($i =0; $i < $count; $i++) {
            $building = new Building();

            $building->address = $this->faker->address;
            $building->location = Building::getPostgisFormatByCoordinate($this->faker->longitude, $this->faker->latitude);

            $building->save();

            print_r($i.PHP_EOL);
        }
    }

    private function openCompanies($count = 100){
        $buildings = Building::find()->asArray()->all();
        $building_ids = ArrayHelper::getColumn($buildings, 'id');

        $building_ids_length = count($building_ids) - 1;
        for($i =0; $i < $count; $i++) {
            $company = new Company();
            $company->title =  $this->faker->company;
            $company->building_id = $building_ids[mt_rand(1, $building_ids_length)];

            $company->save();
            print_r($i.PHP_EOL);
        }
    }

    private function providePhones($count = 150){
        $companies = Company::find()->asArray()->all();
        $company_ids = ArrayHelper::getColumn($companies, 'id');

        $company_ids_length =  count($company_ids) - 1;

        for($i =0; $i < $count; $i++) {
        $company_phone = new CompanyPhone();
            $company_phone->phone = $this->faker->unique()->phoneNumber;
            $company_phone->company_id = $company_ids[mt_rand(1, $company_ids_length)];

            $company_phone->save();
            print_r($i.PHP_EOL);
        }
    }

    private function createRubrics(){

    }


}
