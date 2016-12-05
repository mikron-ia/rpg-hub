<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * EpicQuery represents the model behind the search form about `common\models\Epic`.
 */
final class EpicQuery extends Epic
{
    public function rules()
    {
        return [
            [['epic_id', 'key', 'name', 'system'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params):ActiveDataProvider
    {
        $query = Epic::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
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

    /**
     * @param bool $limitToControlled
     * @return ActiveQuery
     */
    static public function activeEpicsAsActiveRecord($limitToControlled = true):ActiveQuery
    {
        /* @var $user User */
        $user = Yii::$app->user->identity;

        if (Yii::$app->user->can('manager')) {
            /* Admin and manager need them all */
            $query = Epic::find();
        } elseif ($limitToControlled) {
            /* GM needs those mastered and assisted in */
            $query = $user->getEpicsGameMastered();
        } else {
            /* All you participate in */
            $query = $user->getEpics();
        }

        return $query;
    }

    /**
     * @param bool $limitToControlled
     * @return ActiveDataProvider
     */
    static public function activeEpicsAsActiveDataProvider($limitToControlled = true):ActiveDataProvider
    {
        $query = self::activeEpicsAsActiveRecord($limitToControlled);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $dataProvider;
    }

    /**
     * @param bool $limitToControlled
     * @return \yii\db\ActiveRecord[]
     */
    static public function activeEpicsAsModels($limitToControlled = true):array
    {
        if (Yii::$app->user->isGuest) {
            return [];
        }

        $query = self::activeEpicsAsActiveRecord($limitToControlled);

        if (!$query) {
            return [];
        }

        return $query->all();
    }

    /**
     * @return string[]
     */
    static public function getListOfEpicsForSelector():array
    {
        $epicList = self::activeEpicsAsModels(true);

        /** @var string[] $epicListForSelector */
        $epicListForSelector = [];

        foreach ($epicList as $story) {
            $epicListForSelector[$story->epic_id] = $story->name;
        }

        return $epicListForSelector;
    }

    /**
     * @return string[]
     */
    static public function allowedEpics():array
    {
        $ids = [];
        $epics = self::activeEpicsAsModels();

        foreach ($epics as $epic) {
            $ids[] = (int)$epic->epic_id;
        }

        return $ids;
    }
}
