<?php

namespace backend\controllers\tools;

use common\models\core\HasEpicControl;
use common\models\Epic;
use Yii;
use yii\web\NotFoundHttpException;

trait EpicAssistance
{
    /**
     * @param string $key Epic key
     * @param int $epic_id Epic ID
     * @param string $name Epic name
     * @param bool $announce Announced the change to the user?
     */
    public function selectEpic(string $key, int $epic_id, string $name, bool $announce = true): void
    {
        if (empty(Yii::$app->params['activeEpic'])) {
            $this->run('site/set-epic-in-silence', ['epicKey' => $key]);
            if ($announce) {
                Yii::$app->session->setFlash(
                    'success',
                    Yii::t('app', 'EPIC_SET_BASED_ON_OBJECT {epic}', ['epic' => $name])
                );
            }
        } elseif (Yii::$app->params['activeEpic']->epic_id <> $epic_id) {
            $this->run('site/set-epic-in-silence', ['epicKey' => $key]);
            if ($announce) {
                Yii::$app->session->setFlash(
                    'success',
                    Yii::t('app', 'EPIC_CHANGED_BASED_ON_OBJECT {epic}', ['epic' => $name])
                );
            }
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findEpicByKey(string $key): Epic
    {
        $model = Epic::findOne(['key' => $key]);

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'EPIC_NOT_AVAILABLE'));
        }

        return $model;
    }

    protected function getEpicByKeyWithCheck(string $key): Epic
    {
        $epic = $this->findEpicByKey($key);

        if (!$epic->canUserViewYou()) {
            Epic::throwExceptionAboutView();
        }

        $this->selectEpic($epic->key, $epic->epic_id, $epic->name);

        return $epic;
    }

    protected function setEpicOnObject(string $epicKey, HasEpicControl $model): void
    {
        $epic = $this->findEpicByKey($epicKey);
        $epic->canUserViewYou();
        $model->setEpicOnEmpty($epic);
    }
}
