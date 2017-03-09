<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Game;

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

        $query->andFilterWhere(['like', 'time', $this->time])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'details', $this->details])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }

    public function mostRecentDataProvider():ActiveDataProvider
    {
        $query = Game::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            return null;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['position' => SORT_DESC]],
            'pagination' => false,
        ]);

        $query
            ->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id
            ])
            ->orderBy(['position' => SORT_DESC])
            ->limit(4);

        return $dataProvider;
    }
}
