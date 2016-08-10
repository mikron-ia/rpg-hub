<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Parameter;

/**
 * ParameterQuery represents the model behind the search form about `common\models\Parameter`.
 */
final class ParameterQuery extends Parameter
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parameter_id', 'parameter_pack_id', 'position'], 'integer'],
            [['code', 'lang', 'visibility', 'content'], 'safe'],
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
        $query = Parameter::find();

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
