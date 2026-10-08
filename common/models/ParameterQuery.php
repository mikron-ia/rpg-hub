<?php

namespace common\models;

use Override;
use yii\base\Model;
use yii\data\ActiveDataProvider;

final class ParameterQuery extends Parameter
{
    #[Override]
    public function rules(): array
    {
        return [
            [['parameter_id', 'parameter_pack_id', 'position'], 'integer'],
            [['code', 'lang', 'visibility', 'content'], 'safe'],
        ];
    }

    #[Override]
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Parameter::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['position' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'parameter_id' => $this->parameter_id,
            'parameter_pack_id' => $this->parameter_pack_id,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'lang', $this->lang])
            ->andFilterWhere(['like', 'visibility', $this->visibility])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
