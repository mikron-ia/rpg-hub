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
     * @param string[] $params
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Character::find()->joinWith('seenPack', true, 'LEFT JOIN');

        $this->secureQuery($query);

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
     * @param string[] $params
     */
    public function searchForUser(array $params): ActiveDataProvider
    {
        return $this->setUpSearchForUser($this->search($params));
    }

    /**
     * Creates data provider instance with search query applied and applies default order according to time of the last modification
     * This list is more suitable for operator section
     *
     * @param string[] $params
     */
    public function searchForOperator(array $params): ActiveDataProvider
    {
        return $this->setUpSearchForOperator($this->search($params));
    }

    /**
     * Provide all Characters belonging to the given group and visible to the user
     *
     * Note: this method does not account for Group or GroupMembership visibility, only for Character
     * Note: pagination is disabled to avoid conflicts with main tab pagination
     */
    static private function getCharactersToShowInGroupTab(string $groupKey): ActiveDataProvider
    {
        $query = Character::find()
            ->joinWith('groupMembership', true, 'JOIN')
            ->joinWith('groupMembership.group', true, 'JOIN')
            ->where(['group.key' => $groupKey]);

        self::secureQuery($query, 'character');

        return new ActiveDataProvider(['query' => $query, 'pagination' => false]);
    }

    /**
     * Provides data to construct Group tabs with Characters
     *
     * @return CharacterListDataObject[]
     */
    static public function getCharactersToShowInGroupTabAsDataObjects(): array
    {
        return array_map(function (Group $group) {
            return new CharacterListDataObject($group->name, self::getCharactersToShowInGroupTab($group->key));
        }, GroupQuery::listGroupsToShowAsTabs());
    }

    /**
     * Provides list of favorite/starred Characters for active user
     *
     * Note: pagination is disabled to avoid conflicts with main tab pagination
     */
    static function getCharactersToShowInFavoritesTab(): ActiveDataProvider
    {
        $query = Character::find()
            ->joinWith('scribblePack', true, 'JOIN')
            ->joinWith('scribblePack.scribbles', true, 'JOIN')
            ->where([
                'scribble_pack.class' => 'Character',
                'scribble.user_id' => Yii::$app->user->id,
                'scribble.favorite' => true,
            ]);

        self::secureQuery($query, 'character');

        return new ActiveDataProvider(['query' => $query, 'pagination' => false]);
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
