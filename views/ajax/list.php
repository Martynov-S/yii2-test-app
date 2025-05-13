<?php

use yii\bootstrap5\Html;

extract($model_data[0]->attributeAliases(), EXTR_PREFIX_ALL, 'vlma');
foreach ($model_data as $d) {
    if ($active_item == 0) {
        $active_item = $d->$vlma_id;
    }
    ?>
    
    <div class="view-section-item list-item<?= $active_item == $d->$vlma_id ? ' active-item' : '' ?>" data-clickable="Y" data-item-id="<?= $d->$vlma_id ?>">
        <div class="list-item-title">
            <?= Html::encode($d->$vlma_name) ?>
        </div>
        <div class="service-section-item">
            <div class="service-btn" data-item-id="<?= $d->$vlma_id ?>" data-action-type="edit" data-bs-toggle="modal" data-bs-target="#wModal">
                <?= Html::img(Yii::getAlias('@web').'/img/pen.png', ['class' => 'img-edit-item', 'title' => 'Редактировать']) ?>
            </div>
            <div class="service-btn" data-item-id="<?= $d->$vlma_id ?>" data-action-type="del">
                <?= Html::img(Yii::getAlias('@web').'/img/dlt.png', ['class' => 'img-del-item', 'title' => 'Удалить']) ?>
            </div>
        </div>
    </div>
    <?php
}
?>