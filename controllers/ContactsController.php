<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Contacts;
use app\models\Deals;

class ContactsController extends Controller
{
    protected function renderItemForm($model)
    {
        return $this->renderAjax('//ajax/form', [
            'model' => $model,
            'list' => ['attribute' => 'deals', 'data' => $this->getDealsList()],
            'action' => Url::to(['contacts/submit']),
        ]);
    }

    public function actionList()
    {
        $model = Contacts::find()->select(['c_id', 'CONCAT_WS(" ", c_name, c_surname) AS c_name'])->all();
        return $this->renderAjax('//ajax/list', [
            'model_data' => $model, 
            'active_item' => \Yii::$app->request->getBodyParam('id') ?: 0,
        ]);
    }

    public function actionNew()
    {
        $model = new Contacts(['scenario' => Contacts::SCENARIO_NEW_ITEM]);
        $model->loadDefaultValues();
        return $this->renderItemForm($model);
    }

    public function actionEdit()
    {
        $itemId = \Yii::$app->request->getBodyParam('id');
        $model = Contacts::find()->where(['c_id' => $itemId])->with('deals')->one();
        $model->scenario = Contacts::SCENARIO_EDIT_ITEM;
        return $this->renderItemForm($model);
    }

    public function actionDelete()
    {
        $itemId = \Yii::$app->request->getBodyParam('id');
        $model = Contacts::find()->where(['c_id' => $itemId])->with('deals')->one();
        $result = $model->delete();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        if (empty($result)) {
            return [
                'fail' => true,
                'message' => 'Ошибка удаления.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Запись успешно удалена.'
        ];
    }

    public function actionItem()
    {
        $itemId = \Yii::$app->request->getBodyParam('id');
        $model = Contacts::find()->where(['c_id' => $itemId])->with('deals')->one();
        if (empty($model)) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'fail' => true,
                'message' => 'Ничего не найдено.'
            ];
        }

        return $this->renderAjax('//ajax/content', [
            'content' => $model,
            'related' => ['model' => 'deals', 'id' => 'id', 'data' => ['name']],
        ]);
    }

    public function actionSubmit()
    {
        $scenario = \Yii::$app->request->getBodyParam('scenario');

        if ($scenario == Contacts::SCENARIO_NEW_ITEM) {
            $model = new Contacts;
        } elseif ($scenario == Contacts::SCENARIO_EDIT_ITEM) {
            $model = Contacts::find()->where(['c_id' => \Yii::$app->request->getBodyParam('Contacts')['c_id']])->with('deals')->one();
        }
        
        if ($model) {
            $model->scenario = $scenario;

            $model->load(\Yii::$app->request->post());
            if ($model->validate()) {
                $db_deals_ids = ArrayHelper::map($model->deals, 'd_id', 'd_id');
                $form_deals_ids = is_array(\Yii::$app->request->getBodyParam('Contacts')['deals']) ? \Yii::$app->request->getBodyParam('Contacts')['deals'] : [];
                $remove_deals = array_diff($db_deals_ids, $form_deals_ids);
                $add_deals = array_diff($form_deals_ids, $db_deals_ids);

                $model->save(false);
                if (!empty($add_deals)) {
                    $link_deals = Deals::findAll($add_deals);
                    foreach ($link_deals as $deal) {
                        $model->link('deals', $deal);
                    }
                }

                if (!empty($remove_deals)) {
                    $unlink_deals = Deals::findAll($remove_deals);
                    foreach ($unlink_deals as $deal) {
                        $model->unlink('deals', $deal, true);
                    }
                }

                \Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Успешное сохранение данных.',
                    'id' => $model->c_id,
                ];
            }
        }

        return $this->renderItemForm($model);
    }

    protected function getDealsList()
    {
        $model = Deals::find()->select(['d_id', 'd_name'])->orderBy('d_name')->all();
        return ArrayHelper::map($model, 'd_id', 'd_name');
    }
}