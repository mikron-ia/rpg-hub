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

        $language = $descriptionPack->getEpic()->parameterPack->getParameterValueByCode(Parameter::LANGUAGE);
        $model->lang = in_array($language, Yii::$app->params['languagesAvailable']) ? $language : 'en';

        return $model;
    }
}
