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

    public static function findRecursive($children_ids, $direct = 'up', $only_leaf = false)
    {

        $placeholders = (!empty($children_ids))? str_repeat('?,', count($children_ids) - 1) . '?' : false;

        $first_param = ($direct === 'up')? 'id' : 'parent_id';
        $second_param = ($direct === 'up')? 'parent_id' : 'id';

        $sql = 'WITH RECURSIVE tree AS (
                    SELECT
                    *,
                    0 AS level
                    FROM rubric'
                    .(($placeholders) ? ' WHERE '.$first_param.' IN (' . $placeholders . ')' : '')
                    .' UNION
                    SELECT
                      parent.*,
                      child.level + 1 AS level
                    FROM rubric AS parent
                    JOIN tree AS child ON child.'.$second_param.' = parent.'.$first_param.'
                )
               SELECT DISTINCT t.id, t.title, t.parent_id '
	            .' FROM tree as t'
               .(($only_leaf)? ' LEFT OUTER JOIN tree AS r ON t.id = r.parent_id WHERE r.parent_id  IS NULL' : '' )
               .' ORDER BY id ASC;';

        $query = new \yii\db\Query;
        $command = $query->createCommand()->setSql($sql);

        if($placeholders) {
            foreach ($children_ids as $i => $children) {
                $command->bindValue($i + 1, $children);
            }
        }
        $rubric = $command->queryAll();
//        var_export($sql); die();
        return $rubric;
    }

    public static function buildTree(array &$rubrics, $parent_id = NULL)
    {
        $branch = [];

        foreach ($rubrics as &$rubric) {
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
	            unset($rubric);
            }
        }
        return $branch;
    }
}
