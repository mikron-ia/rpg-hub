<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GameQuery represents the model behind the search form about `common\models\Game`.
 */
class GameQuery extends Game
{
    public function rules()
    {
        return [
            [['game_id', 'epic_id', 'position'], 'integer'],
            [['time', 'status', 'details', 'note'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Game::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
            ]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['position' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'basics', $this->basics])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'notes', $this->notes]);

        return $dataProvider;
    }

    public function mostRecentDataProvider(?Epic $activeEpic = null): ?ActiveDataProvider
    {
        $query = Game::find();

        if (empty($activeEpic)) {
            $activeEpic = Yii::$app->params['activeEpic'];
        }

        if (empty($activeEpic)) {
            return null;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['position' => SORT_DESC]],
            'pagination' => false,
        ]);

        $query
            ->andWhere([
                'epic_id' => $activeEpic->epic_id
            ])
            ->orderBy(['position' => SORT_DESC])
            ->limit(4);

        return $dataProvider;
    }

    /**
     * @param array $userIds
     * @return null|ActiveDataProvider
     */
    public function mostRecentByPlayerDataProvider(array $userIds): ?ActiveDataProvider
    {
        $query = Game::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['planned_date' => SORT_DESC]],
            'pagination' => false,
        ]);

        $query
            ->andWhere(['in', 'epic_id', $userIds])
            ->orderBy(['planned_date' => SORT_DESC])
            ->limit(8);

        return $dataProvider;
    }
}
