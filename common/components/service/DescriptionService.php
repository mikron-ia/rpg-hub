<?php

namespace common\components\service;

use common\models\Description;
use common\models\DescriptionPack;
use common\models\Parameter;
use Yii;

class DescriptionService
{
    public static function fillDescription(Description $model, DescriptionPack $descriptionPack): Description
    {
        $model->description_pack_id = $descriptionPack->description_pack_id;

        if (empty($model->lang)) {
            $language = $model->descriptionPack->getEpic()->parameterPack->getParameterValueByCode(Parameter::LANGUAGE);

            if (empty($language) || !in_array($language, Yii::$app->params['languagesAvailable'])) {
                $language = Yii::$app->user->identity->language ?? 'en';
            }

            $model->lang = $language;
        }

        return $model;
    }
}
