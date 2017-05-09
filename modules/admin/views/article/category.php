<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Article */
/* @var $form yii\widgets\ActiveForm */
?>



    <?php $form = ActiveForm::begin(); ?>

    <?= Html::dropDownList('category', $selectCategoryId, $categories, ['class' => 'form-control'])?>
    <div class="form-group">
        <?= Html::submitButton('Submit' , ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>


