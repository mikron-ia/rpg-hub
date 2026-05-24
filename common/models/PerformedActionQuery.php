<?php

namespace common\models;

use Override;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PerformedActionQuery extends PerformedAction
{
    #[Override]
    public function rules(): array
    {
        return [
            [['operation', 'class', 'user_id'], 'safe'],
        ];
    }

    #[Override]
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = PerformedAction::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 16],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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
