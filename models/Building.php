<?php

namespace app\models;

use Yii;

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
            [['location'], 'string'],
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
}
