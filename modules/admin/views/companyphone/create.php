<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CompanyPhone */

$this->title = 'Create Company Phone';
$this->params['breadcrumbs'][] = ['label' => 'Company Phones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-phone-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
