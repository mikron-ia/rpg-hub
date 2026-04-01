<?php

namespace common\models;

use Override;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class PointInTimeQuery extends PointInTime
{
    #[Override]
    public function rules(): array
    {
        return [
            [['name', 'text_public', 'text_protected', 'text_private'], 'safe'],
        ];
    }

    #[Override]
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
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

    public static function pointsInTimeAsActiveRecord(bool $limitToActive = true): ActiveQuery
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

    public static function pointsInTimeAsActiveDataProvider(bool $limitToActive = true): ActiveDataProvider
    {
        $query = self::pointsInTimeAsActiveRecord($limitToActive);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
    }

    /**
     * @return ActiveRecord[]
     */
    public static function pointsInTimeAsModels(bool $limitToActive = true): array
    {
        if (Yii::$app->user->isGuest) {
            return [];
        }

        $query = self::pointsInTimeAsActiveRecord($limitToActive);

        if ($query->exists() === false) {
            return [];
        }

        return $query->all();
    }

    /**
     * @return array<int,string>
     */
    public static function getListOfPointsInTimeForSelector(): array
    {
        $pointsInTimeList = self::pointsInTimeAsModels(true);

        /** @var array<int,string> $pointsInTimeListForSelector */
        $pointsInTimeListForSelector = [];
        foreach ($pointsInTimeList as $pointInTime) {
            /** @var PointInTime $pointInTime */
            $pointsInTimeListForSelector[$pointInTime->point_in_time_id] = $pointInTime->name;
        }

        return $pointsInTimeListForSelector;
    }
}
