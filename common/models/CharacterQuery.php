<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CharacterQuery represents the model behind the search form about `common\models\Character`.
 */
final class CharacterQuery extends Character
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Character::find();

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
            'character_id' => $this->character_id,
            'epic_id' => $this->epic_id,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }

    /**
     * Provides all active characters from the current epic
     * @return Character[]
     */
    static public function activeCharactersAsModels()
    {
        $query = Character::find();

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

        /** @var string $characterListForSelector */
        $characterListForSelector = [];

        foreach ($characterList as $story) {
            $characterListForSelector[$story->character_id] = $story->name;
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
