<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company_rubric".
 *
 * @property integer $rubric_id
 * @property integer $company_id
 *
 * @property Company $company
 * @property Rubric $rubric
 */
class CompanyRubric extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_rubric';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rubric_id', 'company_id'], 'required'],
            [['rubric_id', 'company_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rubric_id' => 'Rubric ID',
            'company_id' => 'Company ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRubric()
    {
        return $this->hasOne(Rubric::className(), ['id' => 'rubric_id']);
    }
}
