<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property integer $id
 * @property string $title
 * @property integer $building_id
 *
 * @property Building $building
 * @property CompanyPhone[] $companyPhones
 * @property CompanyRubric[] $companyRubrics
 * @property Rubric[] $rubrics
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'building_id'], 'required'],
            [['building_id'], 'integer'],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'building_id' => 'Building ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyPhones()
    {
        return $this->hasMany(CompanyPhone::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyRubrics()
    {
        return $this->hasMany(CompanyRubric::className(), ['company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubrics()
    {
        return $this->hasMany(Rubric::className(), ['id' => 'rubric_id'])->viaTable('company_rubric', ['company_id' => 'id']);
    }
}
