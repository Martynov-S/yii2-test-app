<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$form = ActiveForm::begin([
    'id' => 'item-edit-form',
    'action' => $action,
]);
echo Html::hiddenInput('scenario', $model->scenario);

$form_model_attributes = $model->scenarios()[$model->scenario];

foreach ($form_model_attributes as $attribute) {
    if (in_array($attribute, $model->attributeHiddenInput())) {
        echo $form->field($model, $attribute)->hiddenInput()->label(false);
    } else {
        echo $form->field($model, $attribute)->textInput();
    }
}

echo $form->field($model, $list['attribute'])->checkboxList($list['data'], [
    'class' => 'linked-items-list', 
    'itemOptions' => ['wrapperOptions' => ['class' => ['widget' => 'form-check list-item-check']]]
]);

echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary form-sbmt-btn', 'name' => 'contact-button']);
ActiveForm::end();