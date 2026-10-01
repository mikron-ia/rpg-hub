<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ExternalDataQuery extends ExternalData
{
    public function rules(): array
    {
        return [
            [['external_data_id', 'external_data_pack_id'], 'integer'],
            [['code', 'data', 'visibility'], 'safe'],
        ];
    }

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
        $query = ExternalData::find();

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
            'external_data_id' => $this->external_data_id,
            'external_data_pack_id' => $this->external_data_pack_id,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'visibility', $this->visibility]);

        return $dataProvider;
    }
}
