<?php

namespace common\models;

use common\models\core\Visibility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\web\HttpException;

/**
 * StoryQuery represents the model behind the search form about `common\models\Story`.
 */
final class StoryQuery extends Story
{
    private int $pageCount;

    public function __construct($pagination = 4, array $config = [])
    {
        $this->pageCount = $pagination;
        parent::__construct($config);
    }

    public ?string $descriptions = null;

    public function rules(): array
    {
        return [
            [['story_id'], 'integer'],
            [['descriptions'], 'string'],
            [['key', 'name', 'short', 'long', 'data'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        $attributeLabels = parent::attributeLabels();

        $attributeLabels['descriptions'] = Yii::t('app', 'STORY_DESCRIPTIONS');

        return $attributeLabels;
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Story::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
                'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic']),
            ]);
        }

        return $this->getDataProviderForSearch($params, $query);
    }

    /**
     * Creates data provider instance with search query applied and data limited to given Epic
     */
    public function searchForEpic(array $params, Epic $epic = null): ActiveDataProvider
    {
        return $this->getDataProviderForSearch($params, Story::find()->andWhere([
            'epic_id' => $epic->epic_id,
            'visibility' => Visibility::determineVisibilityVector($epic),
        ]));
    }

    /**
     * @param array $userIds
     * @return ArrayDataProvider|null
     * @throws HttpException
     */
    public function mostRecentByPlayerDataProvider(array $userIds): ?ArrayDataProvider
    {
        $query = Story::find()->where(['in', 'epic_id', $userIds])->orderBy([
            'position' => SORT_DESC,
            'story_id' => SORT_DESC
        ]);

        $mostRecentStories = [];

        foreach ($query->all() as $story) {
            /** @var Story $story */
            if (
                !isset($mostRecentStories[$story->epic_id]) &&
                $story->canUserViewYou() &&
                $story->getVisibility() === Visibility::VISIBILITY_FULL
            ) {
                $mostRecentStories[$story->epic_id] = $story;
            }
        }

        return new ArrayDataProvider([
            'allModels' => $mostRecentStories,
            'pagination' => false,
        ]);
    }

    private function getDataProviderForSearch($params, ActiveQuery $query): ActiveDataProvider
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['position' => SORT_DESC]],
            'pagination' => ['pageSize' => $this->pageCount],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere([
                'or',
                ['like', 'short', $this->descriptions],
                ['like', 'long', $this->descriptions]
            ]);

        return $dataProvider;
    }
}
