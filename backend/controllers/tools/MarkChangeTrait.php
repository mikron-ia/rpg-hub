<?php

namespace backend\controllers\tools;

use common\models\core\HasEpicControl;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\HttpException;

trait MarkChangeTrait
{
    /**
     * @param ActiveRecord&HasEpicControl $markedObject
     *
     * @return void
     *
     * @throws HttpException
     */
    private function markChange(ActiveRecord&HasEpicControl $markedObject): void
    {
        if (!$markedObject->canUserControlYou()) {
            $markedObject::throwExceptionAboutControl();
        }

        try {
            if ($markedObject->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'MARK_CHANGE_SUCCESS'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'MARK_CHANGE_ERROR'));
            }
        } catch (Exception) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'MARK_CHANGE_EXCEPTION'));
        }
    }
}
