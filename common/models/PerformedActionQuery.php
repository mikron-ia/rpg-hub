<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PerformedActionQuery represents the model behind the search form about `common\models\PerformedAction`.
 */
class PerformedActionQuery extends PerformedAction
{
    public function rules()
    {
        return [
            [['id', 'object_id', 'performed_at'], 'integer'],
            [['operation', 'class', 'user_id', ], 'safe'],
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
        $query = PerformedAction::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 16],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'object_id' => $this->object_id,
            'performed_at' => $this->performed_at,
        ]);

        $query->andFilterWhere(['in', 'operation', $this->operation])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['in', 'user_id', $this->user_id]);

        return $dataProvider;
    }
}
