<?php

namespace app\controllers;

use yii\helpers\Url;

class StartController extends BaseController
{
    public function actionIndex()
    {
        $list_data = $this->getDealsList();
        $first_id = $list_data[0]->d_id;

        return $this->render('index', [
            'categories' => $this->getCategories(),
            'model_data' => $list_data,
            'content' => $this->getDealDetails($first_id),
            'related' => ['model' => 'contacts', 'id' => 'id', 'data' => ['name', 'surname']],
        ]);
    }

    protected function getCategories()
    {
        return [
            'deals' => [
                'title' => 'Сделки',
                'name' => 'сделку',
                'add_btn_title' => 'Добавить ',
                'modal_titles' => [
                    'add' => 'Добавление новой сделки',
                    'edit' => 'Редактирование сделки',
                ],
                'urls' => [
                    'list' => Url::to(['deals/list']), 
                    'add' => Url::to(['deals/new']), 
                    'edit' => Url::to(['deals/edit']), 
                    'item' => Url::to(['deals/item']),
                    'del' => Url::to(['deals/delete']),
                ],
            ],
            'contacts' => [
                'title' => 'Контакты',
                'name' => 'контакт',
                'add_btn_title' => 'Добавить ',
                'modal_titles' => [
                    'add' => 'Добавление нового контакта',
                    'edit' => 'Редактирование контакта',
                ],
                'urls' => [
                    'list' => Url::to(['contacts/list']), 
                    'add' => Url::to(['contacts/new']), 
                    'edit' => Url::to(['contacts/edit']), 
                    'item' => Url::to(['contacts/item']),
                    'del' => Url::to(['contacts/delete']),
                ],
            ],
        ];
    }
}