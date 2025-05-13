<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use yii\web\View;

$this->title = 'Решение второй части тестового задания';

$this->registerJs("document.addEventListener('DOMContentLoaded', () => { appCategory.init(".json_encode($categories).", 'deals'); })", View::POS_END);

?>
<div class="data-table">
    <div class="table__item themes-list">
        <div class="table__item__title">Меню</div>
        <div id="themes_view" class="table__item__text">
            <?php 
            foreach ($categories as $key => $category) {
                ?>
                <div class="view-section-item menu-item<?= ($key=='deals') ? ' active-item' : '' ?>" data-clickable="Y" data-category-key="<?= $key ?>">
                    <?= Html::encode($category['title']) ?>
                </div>
                <?php 
            }
            ?>
        </div>
    </div>
    <div class="table__item subthemes-list">
        <div id="subthemes_title" class="table__item__title">
            Список
            <span id="item_edit_form" class="subthemes-item-add" title="" data-bs-toggle="modal" data-bs-target="#wModal" data-action-type="add">
                <?= Html::img(Yii::getAlias('@web').'/img/13025660.png', ['class' => 'img-add-item']) ?>
            </span>
        </div>
        <div id="subthemes_view" class="table__item__text">
            <?= $this->render('//ajax/list', ['model_data' => $model_data, 'active_item' => 0]) ?>
        </div>
    </div>
    <div class="table__item last selected-content">
        <div class="table__item__title">Содержимое</div>
        <div id="content_view" class="table__item__text content-view">
            <?= $this->render('//ajax/content', ['content' => $content, 'related' => $related]) ?>
        </div>
    </div>
</div>
<?php
//echo Yii::getAlias('@app');
Modal::begin([
    'id' => 'wModal',
    'title' => 'Добавить',
    'toggleButton' => false,
    'size' => 'modal-lg',
]);
?>
<div id="wModal-form" class="modal-body__form"></div>
<?php
Modal::end();
Modal::begin([
    'id' => 'wmInfo',
    'title' => 'Уведомление',
    'toggleButton' => false,
    'size' => 'modal-sm',
]);
?>
<div id="wmInfo-text" class="modal-body__text"></div>
<?php
Modal::end();