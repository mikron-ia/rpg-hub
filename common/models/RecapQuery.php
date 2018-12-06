<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

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
    public function search($params): ActiveDataProvider
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
            'sort' => ['defaultOrder' => ['position' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'recap_id' => $this->recap_id,
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
        $query = Recap::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            return null;
        }

        $query->andWhere(['epic_id' => Yii::$app->params['activeEpic']->epic_id])->orderBy(['position' => SORT_DESC]);

        /** @var Recap|null $recap */
        $recap = $query->one();

        return $recap;
    }

    /**
     * @param array $userIds
     * @return ArrayDataProvider|null
     */
    public function mostRecentByPlayerDataProvider(array $userIds): ?ArrayDataProvider
    {
        $query = Recap::find()->where(['in', 'epic_id', $userIds])->orderBy(['position' => SORT_DESC, 'recap_id' => SORT_DESC]);

        $mostRecentRecaps = [];

        foreach ($query->all() as $recap)
        {
            /** @var Recap $recap */
            if(!isset($mostRecentRecaps[$recap->epic_id]))
            {
                $mostRecentRecaps[$recap->epic_id] = $recap;
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $mostRecentRecaps,
            'pagination' => false,
        ]);

        return $dataProvider;
    }
}
