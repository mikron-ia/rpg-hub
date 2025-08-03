<?php

namespace common\models;

use common\dto\CharacterListDataObject;
use common\models\core\Visibility;
use common\models\tools\ToolsForImportanceInQueries;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CharacterQuery represents the model behind the search form about `common\models\Character`.
 */
final class CharacterQuery extends Character
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
            [['character_id', 'character_sheet_id'], 'integer'],
            [['epic_id', 'name', 'tagline', 'visibility'], 'safe'],
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
        $query = Character::find()->joinWith('seenPack', true, 'LEFT JOIN');

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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'tagline', $this->tagline])
            ->andFilterWhere(['in', 'visibility', $this->visibility]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied and applies default order according to importance
     * This list is more suitable for front section
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchForUser(array $params): ActiveDataProvider
    {
        return $this->setUpSearchForUser($this->search($params));
    }

    /**
     * Creates data provider instance with search query applied and applies default order according to time of the last modification
     * This list is more suitable for operator section
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchForOperator(array $params): ActiveDataProvider
    {
        return $this->setUpSearchForOperator($this->search($params));
    }

    static private function getCharactersToShowInGroupTab(string $groupKey): ActiveDataProvider
    {
        $query = Character::find()
            ->joinWith('groupMembership', true, 'JOIN')
            ->joinWith('groupMembership.group', true, 'JOIN')
            ->where(['group.key' => $groupKey]);

        self::setUpQuery($query, 'character');

        return new ActiveDataProvider(['query' => $query]);
    }

    /**
     * @return CharacterListDataObject[]
     */
    static public function getCharactersToShowInGroupTabAsDataObjects(): array
    {
        return array_map(function (Group $group) {
            return new CharacterListDataObject($group->name, self::getCharactersToShowInGroupTab($group->key));
        }, GroupQuery::listGroupsToShowAsTabs());
    }

    /**
     * @return string[]
     */
    static public function listEpicCharactersAsArray(): array
    {
        $query = Character::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
                'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic']),
            ]);
        }

        $characters = $query->all();

        $arrayOfNames = [];

        foreach ($characters as $character) {
            /* @var $character Character */
            $arrayOfNames[$character->character_id] = $character->name;
        }

        return $arrayOfNames;
    }
}
