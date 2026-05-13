<?php

namespace common\models;

use common\models\core\Visibility;
use common\models\entities\LocationWithImportance;
use common\models\tools\ToolsForImportanceInQueries;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

/**
 * LocationQuery represents the model behind the search form about `common\models\Location`.
 */
final class LocationQuery extends Location
{
    use ToolsForImportanceInQueries;

    private const int DEFAULT_PAGE_SIZE = 24;

    private int $pageCount;

    public function __construct(int $pagination = self::DEFAULT_PAGE_SIZE, array $config = [])
    {
        $this->pageCount = $pagination;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['name', 'tagline', 'visibility', 'importance_category'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Creates a data provider instance with the search query applied
     *
     * @param string[] $params
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Location::find()->joinWith('seenPack', true, 'LEFT JOIN');

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

        // grid filtering conditions
        $query->andFilterWhere([
            'location_id' => $this->location_id,
            'epic_id' => $this->epic_id,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['tagline', 'name', $this->tagline])
            ->andFilterWhere(['in', 'visibility', $this->visibility])
            ->andFilterWhere(['in', 'importance_category', $this->importance_category]);

        return $dataProvider;
    }

    /**
     * Creates a data provider instance with a search query applied and applies the default order according to importance
     * This list is more suitable for the presentation section
     */
    public function searchForUser(array $params): ActiveDataProvider
    {
        return $this->setUpSearchForUser($this->search($params));
    }

    /**
     * Creates a data provider instance with the search query applied and applies default order according to time of the last modification
     * This list is more suitable for the operator section
     */
    public function searchForOperator(array $params): ActiveDataProvider
    {
        return $this->setUpSearchForOperator($this->search($params));
    }

    /**
     * @param string[] $params
     */
    public function listForOperatorWithImportances(array $params): DataProviderInterface
    {
        $query = Location::find()
            ->joinWith('importancePack', true, 'LEFT JOIN')
            ->joinWith('importancePack.importances', true, 'LEFT JOIN');

        $this->secureQuery($query);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['tagline', 'name', $this->tagline])
            ->andFilterWhere(['in', 'visibility', $this->visibility])
            ->andFilterWhere(['in', 'importance_category', $this->importance_category])
            ->orderBy(['updated_at' => SORT_DESC]);

        $models = [];

        foreach ($dataProvider->getModels() as $location) {
            $locationWithImportance = new LocationWithImportance($location);

            foreach ($location->importancePack->importances as $importance) {
                $locationWithImportance->setImportance($importance->user_id, $importance->importance);
            }

            $fields = array_merge($fields ?? [], $locationWithImportance->getImportanceFieldKeys());
            $models[] = $locationWithImportance;
        }

        return new ArrayDataProvider([
            'allModels' => $models,
            'sort' => [
                'attributes' => array_merge(['name', 'visibility', 'importance_category'], array_unique($fields ?? []))
            ],
            'pagination' => ['pageSize' => $this->pageCount],
        ]);
    }

    /**
     * Provides Locations that should be shown as tabs in the Character index page
     *
     * @return Location[]
     */
    public static function listLocationsToShowAsTabs(): array
    {
        $query = Location::find()->where(['display_as_tab' => true]);

        self::secureQuery($query);

        return (new ActiveDataProvider(['query' => $query]))->getModels();
    }

    /**
     * @return string[]
     */
    static public function getAllFromCurrentEpicForSelector(): array
    {
        $query = Location::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
                'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic']),
            ]);
        }

        /** @var Location[] $records */
        $records = $query->all();

        $list = [];

        foreach ($records as $record) {
            $list[$record->location_id] = $record->name;
        }

        return $list;
    }

    /**
     * @return string[]
     */
    public static function listEpicLocationsAsArray(): array
    {
        $query = Location::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
                'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic']),
            ]);
        }

        $locations = $query->all();

        $arrayOfNames = [];

        foreach ($locations as $location) {
            /* @var $location Location */
            $arrayOfNames[$location->location_id] = $location->name;
        }

        return $arrayOfNames;
    }
}
