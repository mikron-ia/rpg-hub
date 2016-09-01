<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EpicQuery represents the model behind the search form about `common\models\Epic`.
 */
final class EpicQuery extends Epic
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['epic_id', 'key', 'name', 'system'], 'safe'],
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
        $query = Epic::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'epic_id' => $this->epic_id,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'system', $this->system]);

        return $dataProvider;
    }

    static public function activeEpicsAsModels()
    {
        if(Yii::$app->user->isGuest) {
            return [];
        }

        /* @var $user User */
        $user = Yii::$app->user->identity;

        return $user->epics;
    }

    static public function getListOfEpicsForSelector()
    {
        $epicList = self::activeEpicsAsModels();

        /** @var string $epicListForSelector */
        $epicListForSelector = [];

        foreach ($epicList as $story) {
            $epicListForSelector[$story->epic_id] = $story->name;
        }

        return $epicListForSelector;
    }

    static public function allowedEpics()
    {
        return array_keys(self::activeEpicsAsModels());
    }
}
