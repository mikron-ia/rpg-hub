<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CharacterSheetQuery represents the model behind the search form about `common\models\CharacterSheet`.
 */
final class CharacterSheetQuery extends CharacterSheet
{
    public function rules()
    {
        return [
            [['character_sheet_id', 'epic_id'], 'integer'],
            [['key', 'name', 'data'], 'safe'],
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
    public function search($params)
    {
        $query = CharacterSheet::find();

        // add conditions that should always apply here

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
            ]);
        }

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
            'character_sheet_id' => $this->character_sheet_id,
            'epic_id' => $this->epic_id,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }

    public function searchForFront($params)
    {
        $search = $this->search($params);

        if (!Participant::participantHasRole(Yii::$app->user->identity, Yii::$app->params['activeEpic'], ParticipantRole::ROLE_GM)) {
            $search->query->andWhere(['player_id' => Yii::$app->user->id]);
        }

        return $search;
    }

    /**
     * Provides all active characters from the current epic
     * @return CharacterSheet[]
     */
    static public function activeCharactersAsModels()
    {
        $query = CharacterSheet::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $dataProvider->getModels();
    }

    /**
     * @return string[]
     */
    static public function getListOfCharactersForSelector()
    {
        $characterList = self::activeCharactersAsModels();

        /** @var string[] $characterListForSelector */
        $characterListForSelector = [];

        foreach ($characterList as $story) {
            $characterListForSelector[$story->character_sheet_id] = $story->name;
        }

        return $characterListForSelector;
    }

    /**
     * @return int[]
     */
    static public function allowedCharacters()
    {
        return array_keys(self::activeCharactersAsModels());
    }
}
