<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rubric".
 *
 * @property integer $id
 * @property string $title
 * @property integer $parent_id
 *
 * @property CompanyRubric[] $companyRubrics
 * @property Company[] $companies
 */
class Rubric extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rubric';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['parent_id'], 'integer'],
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
            'parent_id' => 'Parent ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyRubrics()
    {
        return $this->hasMany(CompanyRubric::className(), ['rubric_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['id' => 'company_id'])->viaTable('company_rubric', ['rubric_id' => 'id']);
    }

    public static function findRecursive($children_ids)
    {

        $placeholders = (!empty($children_ids)) ?str_repeat('?,', count($children_ids) - 1) . '?' : false;

        $sql = 'WITH RECURSIVE tree AS (
                    SELECT
                    *,
                    0 AS level
                    FROM rubric'
                    .(($placeholders) ? ' WHERE id IN (' . $placeholders . ')' : '')
                    .' UNION
                    SELECT
                      parent.*,
                      child.level + 1 AS level
                    FROM rubric AS parent
                      JOIN tree AS child ON child.parent_id = parent.id
                )
               SELECT DISTINCT id, title, parent_id
               FROM tree
               ORDER BY id ASC;';

        $query = new \yii\db\Query;
        $command = $query->createCommand()->setSql($sql);

        if($placeholders) {
            foreach ($children_ids as $i => $children) {
                $command->bindValue($i + 1, $children);
            }
        }
        $rubric = $command->queryAll();

        return $rubric;
    }

    public static function buildTree(array $rubrics, $parent_id = 0)
    {
        $branch = array();

        foreach ($rubrics as $rubric) {
            if ($rubric['parent_id'] == $parent_id) {
                $children = self::buildTree($rubrics, $rubric['id']);
                if ($children) {
                    $rubric['subrubrics'] = $children;
                }
                $branch[] = [
                    'id' => $rubric['id'],
                    'title' => $rubric['title'],
                    'parent_id' => $rubric['parent_id'],
                    'subrubrics' => isset($rubric['subrubrics'])? $rubric['subrubrics'] : []
                ];
            }
        }
        return $branch;
    }
}
