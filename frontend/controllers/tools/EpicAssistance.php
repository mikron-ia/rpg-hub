<?php

namespace frontend\controllers\tools;

use common\models\Epic;
use Yii;
use yii\web\NotFoundHttpException;

trait EpicAssistance
{
    /**
     * @param string $key Epic key
     * @param int $epic_id Epic ID
     * @param string $name Epic name
     */
    public function selectEpic(string $key, int $epic_id, string $name): void
    {
        if (empty(Yii::$app->params['activeEpic'])) {
            $this->run('site/set-epic-in-silence', ['epicKey' => $key]);
            Yii::$app->session->setFlash(
                'success',
                Yii::t('app', 'EPIC_SET_BASED_ON_OBJECT {epic}', ['epic' => $name])
            );
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $epic_id) {
            $this->run('site/set-epic-in-silence', ['epicKey' => $key]);
            Yii::$app->session->setFlash(
                'success',
                Yii::t('app', 'EPIC_CHANGED_BASED_ON_OBJECT {epic}', ['epic' => $name])
            );
        }
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $key
     * @return Epic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findEpicByKey($key)
    {
        $model = Epic::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'EPIC_NOT_AVAILABLE'));
        }

        return $model;
    }
}
