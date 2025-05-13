<?php

use yii\bootstrap5\Html;

?>

<table class="content-table">
    <?php 
    foreach ($content->attributes() as $attribute) {
        ?>
        <tr>
            <td class="td-title"><?= $content->getAttributeLabel($attribute) ?></td>
            <td><?= $content->$attribute ?></td>
        </tr>
        <?php
    }

    foreach ($content->{$related['model']} as $related_item) {
        ?>
        <tr>
            <td class="td-title">
                <?= $related_item->getAttributeLabel($related_item->getAttributeByAlias($related['id'])) ?>: <?= $related_item->{$related_item->getAttributeByAlias($related['id'])} ?>
            </td>
            <td>
                <?php
                $td_data = '';
                foreach ($related['data'] as $field) {
                     $td_data .= $related_item->{$related_item->getAttributeByAlias($field)} . ' ';
                }
                echo trim($td_data);
                ?>
            </td>
        </tr>
        <?php
    }
    ?>
</table>