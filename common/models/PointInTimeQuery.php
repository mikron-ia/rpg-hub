<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * PointInTimeQuery represents the model behind the search form about `common\models\PointInTime`.
 */
class PointInTimeQuery extends PointInTime
{
    public function rules()
    {
        return [
            [['name', 'text_public', 'text_protected', 'text_private'], 'safe'],
        ];
    }

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
        $query = PointInTime::find();

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
            'sort' => ['defaultOrder' => ['position' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'text_public', $this->text_public])
            ->andFilterWhere(['like', 'text_protected', $this->text_protected])
            ->andFilterWhere(['like', 'text_private', $this->text_private]);

        return $dataProvider;
    }

    /**
     * @param bool $limitToActive
     * @return ActiveQuery
     */
    static public function pointsInTimeAsActiveRecord($limitToActive = true): ActiveQuery
    {
        $query = PointInTime::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            $query->where('0=1');
        } else {
            $query->where([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
            ]);
        }

        if($limitToActive) {
            $query->andWhere(['in', 'status', [PointInTime::STATUS_ACTIVE]]);
        }

        $query->orderBy('position DESC');

        return $query;
    }

    /**
     * @param bool $limitToActive
     * @return ActiveDataProvider
     */
    static public function pointsInTimeAsActiveDataProvider($limitToActive = true): ActiveDataProvider
    {
        $query = self::pointsInTimeAsActiveRecord($limitToActive);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $dataProvider;
    }

    /**
     * @param bool $limitToActive
     * @return \yii\db\ActiveRecord[]
     */
    static public function pointsInTimeAsModels($limitToActive = true): array
    {
        if (Yii::$app->user->isGuest) {
            return [];
        }

        $query = self::pointsInTimeAsActiveRecord($limitToActive);

        if (!$query) {
            return [];
        }

        return $query->all();
    }

    /**
     * @return string[]
     */
    static public function getListOfPointsInTimeForSelector(): array
    {
        $pointsInTimeList = self::pointsInTimeAsModels(true);

        /** @var string[] $pointsInTimeListForSelector */
        $pointsInTimeListForSelector = [];

        foreach ($pointsInTimeList as $pointInTime) {
            $pointsInTimeListForSelector[$pointInTime->point_in_time_id] = $pointInTime->name;
        }

        return $pointsInTimeListForSelector;
    }
}
