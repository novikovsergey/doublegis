<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company_phone".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $phone
 *
 * @property Company $company
 */
class CompanyPhone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_phone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'phone'], 'required'],
            [['company_id'], 'integer'],
            [['phone'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'phone' => 'Phone',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }
}
