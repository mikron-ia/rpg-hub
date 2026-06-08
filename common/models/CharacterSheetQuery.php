<?php

namespace common\models;

use Override;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

final class CharacterSheetQuery extends CharacterSheet
{
    #[Override]
    public function rules(): array
    {
        return [
            [['key', 'name', 'data'], 'safe'],
        ];
    }

    #[Override]
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates a data provider instance with the search query applied
     */
    public function search(array $params): ActiveDataProvider
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

    public function searchForFront(array $params): ActiveDataProvider
    {
        $search = $this->search($params);

        if (!Participant::participantHasRole(
            Yii::$app->user->identity,
            Yii::$app->params['activeEpic'],
            ParticipantRole::ROLE_GM)
        ) {
            $search->query->andWhere(['player_id' => Yii::$app->user->id]);
        }

        return $search;
    }

    /**
     * Provides all active characters from the current epic
     *
     * @return CharacterSheet[]
     */
    public static function activeCharactersAsModels(): array
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
    public static function getListOfCharactersForSelector(): array
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
    public static function allowedCharacters(): array
    {
        return array_keys(self::activeCharactersAsModels());
    }
}
