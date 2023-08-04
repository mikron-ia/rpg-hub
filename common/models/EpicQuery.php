<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
    public function search($params): ActiveDataProvider
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
    static public function activeEpicsAsActiveRecord(bool $limitToControlled = true): ActiveQuery
    {
        /* @var $user User */
        $user = Yii::$app->user->identity;

        if (Yii::$app->user->can('manager')) {
            /* GM needs those mastered and assisted in */
            $query = $user->getEpicsGameMasteredAndManaged();
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
    static public function activeEpicsAsActiveDataProvider(bool $limitToControlled = true): ActiveDataProvider
    {
        $query = self::activeEpicsAsActiveRecord($limitToControlled);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    /**
     * @param bool $limitToControlled
     * @return ActiveRecord[]
     */
    static public function activeEpicsAsModels(bool $limitToControlled = true): array
    {
        if (Yii::$app->user->isGuest) {
            return [];
        }

        $query = self::activeEpicsAsActiveRecord($limitToControlled);

        if (!$query) {
            return [];
        }

        return self::sortByStatus($query->all());
    }

    /**
     * @return string[]
     */
    static public function getListOfEpicsForSelector(): array
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
     * @param bool $limitToControlled
     * @return string[]
     */
    static public function allowedEpics(bool $limitToControlled = true): array
    {
        $ids = [];
        $epics = self::activeEpicsAsModels($limitToControlled);

        foreach ($epics as $epic) {
            $ids[] = (int)$epic->epic_id;
        }

        return $ids;
    }

    /**
     * Provides list of all epics, with indication of user's role in them
     * @return ActiveDataProvider
     */
    static public function manageableEpicsAsActiveDataProvider(): ActiveDataProvider
    {
        $query = Epic::find()
            ->joinWith('participants', true, 'LEFT JOIN')
            ->joinWith('participants.participantRoles', true, 'LEFT JOIN');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }
}
