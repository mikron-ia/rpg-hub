<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Description;

/**
 * DescriptionQuery represents the model behind the search form about `common\models\Description`.
 */
class DescriptionQuery extends Description
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description_pack_id'], 'integer'],
            [['title', 'code', 'public_text', 'private_text', 'lang', 'visibility'], 'safe'],
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
        $query = Description::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'description_pack_id' => $this->description_pack_id,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'public_text', $this->public_text])
            ->andFilterWhere(['like', 'private_text', $this->private_text])
            ->andFilterWhere(['in', 'lang', $this->lang])
            ->andFilterWhere(['in', 'visibility', $this->visibility]);

        return $dataProvider;
    }
}
