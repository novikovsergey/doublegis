<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Rubric */

$this->title = 'Create Rubric';
$this->params['breadcrumbs'][] = ['label' => 'Rubrics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rubric-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
