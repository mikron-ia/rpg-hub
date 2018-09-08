<?php

namespace backend\controllers\tools;

use Yii;

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
}
