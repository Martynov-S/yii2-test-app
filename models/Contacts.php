<?php

namespace app\models;

class Contacts extends AppBaseModel
{

    const SCENARIO_NEW_ITEM = 'new';
    const SCENARIO_EDIT_ITEM = 'edit';

    public function scenarios()
    {
        return [
            self::SCENARIO_NEW_ITEM => ['c_name', 'c_surname'],
            self::SCENARIO_EDIT_ITEM => ['c_id', 'c_name', 'c_surname'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'c_id' => 'id контакта',
            'c_name' => 'Имя',
            'c_surname' => 'Фамилия',
            'deals' => 'Сделки',
        ];
    }

    public function attributeAliases()
    {
        return [
            'id' => 'c_id',
            'name' => 'c_name',
            'surname' => 'c_surname',
        ];
    }

    public function attributeHiddenInput()
    {
        return [
            'c_id',
        ];
    }

    public function rules()
    {
        return [
            ['c_name', 'required'],
            [['c_name', 'c_surname'], 'trim'],
            ['c_name', 'string', 'length' => [2, 150]],
        ];
    }

    public function getDeals()
    {
        return $this->hasMany(Deals::class, ['d_id' => 'cod_did'])->viaTable('contacts_of_deals', ['cod_cid' => 'c_id']);
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        foreach ($this->deals as $deal) {
            $this->unlink('deals', $deal, true);
        }
        return true;
    }
}