<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Deals;

class BaseController extends Controller
{
    protected function getDealsList()
    {
        $model = Deals::find()->select(['d_id', 'd_name'])->all();
        return $model;
    }

    protected function getDealDetails($id)
    {
        $model = Deals::find()->where(['d_id' => $id])->with('contacts')->one();
        return $model;
    }
}