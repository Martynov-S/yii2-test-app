<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Deals;
use app\models\Contacts;

class DealsController extends Controller
{
    protected function renderItemForm($model)
    {
        return $this->renderAjax('//ajax/form', [
            'model' => $model,
            'list' => ['attribute' => 'contacts', 'data' => $this->getContactsList()],
            'action' => Url::to(['deals/submit']),
        ]);
    }

    public function actionList()
    {
        $model = Deals::find()->select(['d_id', 'd_name'])->all();
        return $this->renderAjax('//ajax/list', [
            'model_data' => $model, 
            'active_item' => \Yii::$app->request->getBodyParam('id') ?: 0,
        ]);
    }

    public function actionNew()
    {
        $model = new Deals(['scenario' => Deals::SCENARIO_NEW_ITEM]);
        $model->loadDefaultValues();
        return $this->renderItemForm($model);
    }

    public function actionEdit()
    {
        $itemId = \Yii::$app->request->getBodyParam('id');
        $model = Deals::find()->where(['d_id' => $itemId])->with('contacts')->one();
        $model->scenario = Deals::SCENARIO_EDIT_ITEM;
        return $this->renderItemForm($model);
    }

    public function actionDelete()
    {
        $itemId = \Yii::$app->request->getBodyParam('id');
        $model = Deals::find()->where(['d_id' => $itemId])->with('contacts')->one();
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
        $model = Deals::find()->where(['d_id' => $itemId])->with('contacts')->one();
        if (empty($model)) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'fail' => true,
                'message' => 'Ничего не найдено.'
            ];
        }

        return $this->renderAjax('//ajax/content', [
            'content' => $model,
            'related' => ['model' => 'contacts', 'id' => 'id', 'data' => ['name', 'surname']],
        ]);
    }

    public function actionSubmit()
    {
        $scenario = \Yii::$app->request->getBodyParam('scenario');

        if ($scenario == Deals::SCENARIO_NEW_ITEM) {
            $model = new Deals;
        } elseif ($scenario == Deals::SCENARIO_EDIT_ITEM) {
            $model = Deals::find()->where(['d_id' => \Yii::$app->request->getBodyParam('Deals')['d_id']])->with('contacts')->one();
        }

        if ($model) {        
            $model->scenario = $scenario;
        
            $model->load(\Yii::$app->request->post());

            if ($model->validate()) {
                $db_contact_ids = ArrayHelper::map($model->contacts, 'c_id', 'c_id');
                $form_contact_ids = is_array(\Yii::$app->request->getBodyParam('Deals')['contacts']) ? \Yii::$app->request->getBodyParam('Deals')['contacts'] : [];
                $remove_contacts = array_diff($db_contact_ids, $form_contact_ids);
                $add_contacts = array_diff($form_contact_ids, $db_contact_ids);

                $model->save(false);
                if (!empty($add_contacts)) {
                    $link_contacts = Contacts::findAll($add_contacts);
                    foreach ($link_contacts as $contact) {
                        $model->link('contacts', $contact);
                    }
                }

                if (!empty($remove_contacts)) {
                    $unlink_contacts = Contacts::findAll($remove_contacts);
                    foreach ($unlink_contacts as $contact) {
                        $model->unlink('contacts', $contact, true);
                    }
                }

                \Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Успешное сохранение данных.',
                    'id' => $model->d_id,
                ];
            }
        }

        return $this->renderItemForm($model);
    }

    protected function getContactsList()
    {
        $model = Contacts::find()->select(['c_id', 'CONCAT_WS(" ", c_surname, c_name) AS c_name'])->orderBy('c_name')->all();
        return ArrayHelper::map($model, 'c_id', 'c_name');
    }
}