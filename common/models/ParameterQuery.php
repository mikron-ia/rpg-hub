<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ParameterQuery represents the model behind the search form about `common\models\Parameter`.
 */
final class ParameterQuery extends Parameter
{
    public function rules()
    {
        return [
            [['parameter_id', 'parameter_pack_id', 'position'], 'integer'],
            [['code', 'lang', 'visibility', 'content'], 'safe'],
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
