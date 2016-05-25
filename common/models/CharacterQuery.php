<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CharacterQuery represents the model behind the search form about `common\models\Character`.
 */
class CharacterQuery extends Character
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['character_id', 'epic_id'], 'integer'],
            [['key', 'name', 'data'], 'safe'],
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
        $query = Character::find();

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
            'character_id' => $this->character_id,
            'epic_id' => $this->epic_id,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }

    static public function activeCharactersAsModels()
    {
        $query = Character::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider->getModels();
    }

    static public function getListOfCharactersForSelector()
    {
        $characterList = self::activeCharactersAsModels();

        /** @var string $characterListForSelector */
        $characterListForSelector = [];

        foreach ($characterList as $story) {
            $characterListForSelector[$story->character_id] = $story->name;
        }

        return $characterListForSelector;
    }

    static public function allowedCharacters()
    {
        return array_keys(self::activeCharactersAsModels());
    }
}
