<?php

namespace common;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Scenario;

/**
 * ScenarioQuery represents the model behind the search form about `common\models\Scenario`.
 */
class ScenarioQuery extends Scenario
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['scenario_id', 'epic_id', 'description_pack_id'], 'integer'],
            [['name', 'tag_line'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
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
        $query = Scenario::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'scenario_id' => $this->scenario_id,
            'epic_id' => $this->epic_id,
            'description_pack_id' => $this->description_pack_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'tag_line', $this->tag_line]);

        return $dataProvider;
    }
}
