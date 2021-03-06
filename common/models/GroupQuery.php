<?php

namespace common\models;

use common\models\core\Visibility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GroupQuery represents the model behind the search form about `common\models\Group`.
 */
final class GroupQuery extends Group
{
    /**
     * @var int
     */
    private $pageCount;

    public function __construct($pagination = 24, array $config = [])
    {
        $this->pageCount = $pagination;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['group_id', 'epic_id'], 'integer'],
            [['key', 'name', 'data'], 'safe'],
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
    public function search($params)
    {
        $query = Group::find()->joinWith('seenPack', true, 'LEFT JOIN');

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
                'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic']),
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $this->pageCount],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'group_id' => $this->group_id,
            'epic_id' => $this->epic_id,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }

    static public function getAllFromCurrentEpicForSelector()
    {
        $query = Group::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
                'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic']),
            ]);
        }

        /** @var Group[] $records */
        $records = $query->all();

        $list = [];

        foreach ($records as $record) {
            $list[$record->group_id] = $record->name;
        }

        return $list;
    }
}
