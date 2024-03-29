<?php

namespace common\models\tools;

use common\models\core\Visibility;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

trait ToolsForImportanceInQueries
{
    /**
     * Adds sorting by importance to the data provider
     *
     * @param ActiveDataProvider $search
     *
     * @return ActiveDataProvider
     */
    private function setUpSearchForUser(ActiveDataProvider $search): ActiveDataProvider
    {
        $search->query->joinWith([
            'importancePack.importances' => function ($query) {
                $query->orderBy(['importance' => SORT_DESC]);
            }
        ])->andWhere(['user_id' => Yii::$app->user->id]);

        return $search;
    }

    /**
     * Adds sorting by last update to the data provider
     *
     * @param ActiveDataProvider $search
     * @return ActiveDataProvider
     */
    private function setUpSearchForOperator(ActiveDataProvider $search): ActiveDataProvider
    {
        $search->sort = ['defaultOrder' => ['updated_at' => SORT_DESC]];

        return $search;
    }

    /**
     * Sets up the basic query - checks for Epic, filters by Epic and visibility
     *
     * @param ActiveQuery $query
     * @return ActiveQuery
     */
    private function setUpQuery(ActiveQuery $query): ActiveQuery
    {
        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
                'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic']),
            ]);
        }

        return $query;
    }
}
