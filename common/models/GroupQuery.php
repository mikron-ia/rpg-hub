<?php

namespace common\models;

use common\models\core\Visibility;
use common\models\tools\ToolsForImportanceInQueries;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GroupQuery represents the model behind the search form about `common\models\Group`.
 */
final class GroupQuery extends Group
{
    use ToolsForImportanceInQueries;

    private const DEFAULT_PAGE_SIZE = 24;

    private int $pageCount;

    public function __construct(int $pagination = self::DEFAULT_PAGE_SIZE, array $config = [])
    {
        $this->pageCount = $pagination;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['group_id', 'epic_id'], 'integer'],
            [['key', 'name', 'data'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Group::find()->joinWith('seenPack', true, 'LEFT JOIN');

        $this->setUpQuery($query);

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

    /**
     * Creates data provider instance with search query applied and applies default order according to importance
     * This list is more suitable for front section
     */
    public function searchForUser(array $params): ActiveDataProvider
    {
        return $this->setUpSearchForUser($this->search($params));
    }

    /**
     * Creates data provider instance with search query applied and applies default order according to time of the last modification
     * This list is more suitable for operator section
     */
    public function searchForOperator(array $params): ActiveDataProvider
    {
        return $this->setUpSearchForOperator($this->search($params));
    }

    /**
     * @return Group[]
     */
    public static function listGroupsToShowAsTabs(): array
    {
        $query = Group::find()->where(['display_as_tab' => true]);

        self::setUpQuery($query);

        return (new ActiveDataProvider(['query' => $query]))->getModels();
    }

    /**
     * @return string[]
     */
    static public function getAllFromCurrentEpicForSelector(): array
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
