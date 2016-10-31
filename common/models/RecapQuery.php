<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * RecapQuery represents the model behind the search form about `common\models\Recap`.
 */
final class RecapQuery extends Recap
{
    public function rules()
    {
        return [
            [['recap_id'], 'integer'],
            [['key', 'name', 'data', 'time'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params):ActiveDataProvider
    {
        $query = Recap::find();

        // add conditions that should always apply here

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['time' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'recap_id' => $this->recap_id,
            'time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }

    /**
     * @return Recap|null
     */
    public function mostRecent()
    {
        $provider = $this->search([]);
        $provider->query->limit(1);

        $models = $provider->getModels();

        if($models) {
            return array_pop($models);
        } else {
            return null;
        }
    }
}
