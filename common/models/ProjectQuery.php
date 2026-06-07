<?php

namespace common\models;

use common\models\core\EntityQuery;
use common\models\core\Visibility;
use Override;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\web\HttpException;

final class ProjectQuery extends Project implements EntityQuery
{
    private const int DEFAULT_PAGE_SIZE = 4;

    private int $pageCount;

    public ?string $descriptions = null;

    public ?string $parameters = null;

    public function __construct($pagination = self::DEFAULT_PAGE_SIZE, array $config = [])
    {
        $this->pageCount = $pagination;
        parent::__construct($config);
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['descriptions', 'name', 'parameters'], 'string'],
            [['code', 'visibility'], 'safe'],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        $attributeLabels = parent::attributeLabels();

        $attributeLabels['descriptions'] = Yii::t('app', 'PROJECT_DESCRIPTIONS');
        $attributeLabels['parameters'] = Yii::t('app', 'PROJECT_PARAMETERS');

        return $attributeLabels;
    }

    #[Override]
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    #[Override]
    public function search(array $params): ActiveDataProvider
    {
        $query = Project::find()->joinWith('parameterPack', true, 'JOIN');

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
     * Creates a data provider instance with the search query applied and data limited to given Epic
     *
     * @todo Add limitation by status
     */
    public function searchForEpic(array $params, ?Epic $epic = null): ActiveDataProvider
    {
        return $this->getDataProviderForSearch($params, Project::find()->andWhere([
            'epic_id' => $epic->epic_id,
            'visibility' => Visibility::determineVisibilityVector($epic),
        ]));
    }

    /**
     * @param array<int> $userIds
     * @throws HttpException
     */
    public function mostRecentByPlayerDataProvider(array $userIds): ?ArrayDataProvider
    {
        $query = Project::find()->where(['in', 'epic_id', $userIds])->orderBy([
            'position' => SORT_DESC,
            'project_id' => SORT_DESC,
        ]);

        $mostRecentProjects = [];

        foreach ($query->all() as $project) {
            /** @var Project $project */
            if (
                !isset($mostRecentProjects[$project->epic_id]) &&
                $project->canUserViewYou() &&
                $project->getVisibility() === Visibility::VISIBILITY_FULL
            ) {
                $mostRecentProjects[$project->epic_id] = $project;
            }
        }

        return new ArrayDataProvider([
            'allModels' => $mostRecentProjects,
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

        $visibilityColumn = in_array(
            Visibility::VISIBILITY_GM->value,
            Visibility::determineVisibilityVector(Yii::$app->params['activeEpic'])
        ) ? 'parameters_gm' : 'parameters_full';

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['in', 'code', $this->code])
            ->andFilterWhere(['in', 'visibility', $this->visibility])
            ->andFilterWhere([
                'or',
                ['like', 'short', $this->descriptions],
                ['like', 'long', $this->descriptions],
            ])->andFilterWhere(['like', $visibilityColumn, $this->parameters]);

        return $dataProvider;
    }

    /**
     * @return string[]
     */
    public static function listEpicProjectsAsArrayForDropdown(Epic $epic): array
    {
        $query = Project::find();

        $query->andWhere([
            'epic_id' => $epic->epic_id,
            'visibility' => Visibility::determineVisibilityVector($epic),
        ])->orderBy('position DESC');

        $projects = $query->all();

        $arrayOfNames = [];

        foreach ($projects as $project) {
            /* @var $project Project */
            $arrayOfNames[$project->project_id] = $project->name;
        }

        return $arrayOfNames;
    }
}
