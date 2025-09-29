<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ScenarioQuery represents the model behind the search form about `common\models\Scenario`.
 */
class ScenarioQuery extends Scenario
{
    public function rules(): array
    {
        return [
            [['scenario_id', 'epic_id', 'description_pack_id'], 'integer'],
            [['name', 'tag_line', 'status'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    static public function allFromCurrentEpicForSelector(): array
    {
        $query = Scenario::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            $query->where('0=1');
        } else {
            $query->andWhere(['epic_id' => Yii::$app->params['activeEpic']->epic_id])->orderBy('scenario_id DESC');
        }

        /** @var Scenario[] $records */
        $records = $query->all();

        $list = [];

        foreach ($records as $record) {
            $list[$record->scenario_id] = $record->name;
        }

        return $list;
    }

    /**
     * Creates a data provider instance with a search query applied
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Scenario::find();

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
            'sort' => ['defaultOrder' => ['status' => SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'scenario_id' => $this->scenario_id,
            'epic_id' => $this->epic_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['in', 'status', $this->status])
            ->andFilterWhere(['like', 'tag_line', $this->tag_line]);

        return $dataProvider;
    }
}
