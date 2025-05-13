<?php

namespace app\models;

class Deals extends AppBaseModel
{
    const SCENARIO_NEW_ITEM = 'new';
    const SCENARIO_EDIT_ITEM = 'edit';

    public function scenarios()
    {
        return [
            self::SCENARIO_NEW_ITEM => ['d_name', 'd_sum'],
            self::SCENARIO_EDIT_ITEM => ['d_id', 'd_name', 'd_sum'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'd_id' => 'id сделки',
            'd_name' => 'Наименование сделки',
            'd_sum' => 'Сумма сделки',
            'contacts' => 'Контакты',
        ];
    }

    public function attributeAliases()
    {
        return [
            'id' => 'd_id',
            'name' => 'd_name',
            'sum' => 'd_sum',
        ];
    }

    public function attributeHiddenInput()
    {
        return [
            'd_id',
        ];
    }

    public function rules()
    {
        return [
            ['d_name', 'required'],
            [['d_name', 'd_sum'], 'trim'],
            ['d_name', 'string', 'length' => [5, 250]],
            ['d_sum', 'number', 'min' => 0],
        ];
    }

    public function getContacts()
    {
        return $this->hasMany(Contacts::class, ['c_id' => 'cod_cid'])->viaTable('contacts_of_deals', ['cod_did' => 'd_id']);
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        foreach ($this->contacts as $contact) {
            $this->unlink('contacts', $contact, true);
        }
        return true;
    }
}