<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "building".
 *
 * @property integer $id
 * @property string $address
 * @property string $location
 *
 * @property Company[] $companies
 */
class Building extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'building';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address', 'location'], 'required'],
            [['address'], 'string', 'max' => 255]
        ];
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address' => 'Address',
            'location' => 'Location',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['building_id' => 'id']);
    }

    public static function locationStringToGeometry($location)
    {
        if (preg_match('/^(\-?\d+(\.\d+)?),(\-?\d+(\.\d+)?)$/', $location, $match)) {

            $location = \yii\helpers\StringHelper::explode($location, ',');
            return self::getPostgisFormatByCoordinate($location[0], $location[1]);
        }
        return null;
    }

    public static function getPostgisFormatByCoordinate($x, $y) {
        return new Expression('ST_SetSRID(ST_MakePoint('.$x.','.$y.'), 4326)');
    }
}
