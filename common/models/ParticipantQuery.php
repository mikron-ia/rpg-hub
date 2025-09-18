<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ParticipantQuery represents the model behind the search form about `common\models\Participant`.
 */
final class ParticipantQuery extends Participant
{
    public function rules()
    {
        return [
            [['participant_id', 'user_id', 'epic_id'], 'integer'],
        ];
    }

    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates a data provider instance with a search query applied
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Participant::find();

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
            'user_id' => $this->user_id,
            'epic_id' => $this->epic_id,
        ]);

        return $dataProvider;
    }
}
