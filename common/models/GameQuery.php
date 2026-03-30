<?php

namespace common\models;

use Override;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class GameQuery extends Game
{
    #[Override]
    public function rules(): array
    {
        return [
            [['game_id', 'epic_id', 'position'], 'integer'],
            [['time', 'status', 'details', 'note'], 'safe'],
        ];
    }

    #[Override]
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Creates a data provider instance with the search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
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
     * @return ActiveDataProvider|null
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
