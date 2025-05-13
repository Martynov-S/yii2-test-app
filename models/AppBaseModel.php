<?php
namespace app\models;

use yii\db\ActiveRecord;

class AppBaseModel extends ActiveRecord
{
    public function attributeAliases()
    {
        return [];
    }

    public function getAttributeByAlias($alias)
    {
        $aliases = $this->attributeAliases();
        return isset($aliases[$alias]) ? $aliases[$alias] : '';
    }
}

