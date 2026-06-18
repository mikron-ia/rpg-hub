<?php

namespace common\models;

use common\models\core\Visibility;
use Override;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;
use yii\data\Sort;

class ArticleQuery extends Article
{
    #[Override]
    public function rules(): array
    {
        return [
            [['article_id', 'epic_id'], 'integer'],
            [['key', 'title', 'subtitle', 'visibility', 'text_raw', 'text_ready'], 'safe'],
        ];
    }

    #[Override]
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Creates a data provider instance with the search query applied
     *
     * @param array<string,string> $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Article::find();

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
            'sort' => ['defaultOrder' => ['position' => SORT_DESC]],
            'pagination' => ['pageSize' => 8],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'article_id' => $this->article_id,
            'epic_id' => $this->epic_id,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'subtitle', $this->subtitle])
            ->andFilterWhere(['like', 'visibility', $this->visibility])
            ->andFilterWhere(['like', 'text_raw', $this->text_raw])
            ->andFilterWhere(['like', 'text_ready', $this->text_ready]);

        return $dataProvider;
    }

    public function searchForUser(): DataProviderInterface
    {
        $query = Article::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
                'visibility' => Visibility::determineUnsafeVisibilityVector(Yii::$app->params['activeEpic']),
            ]);
        }

        return new ArrayDataProvider([
            'allModels' => array_filter($query->all(), function (Article $model) {
                return $model->getVisibility() !== Visibility::VISIBILITY_DESIGNATED || $model->canUserViewYou();
            }),
            'sort' => new Sort([
                'attributes' => [
                    'position',
                ],
                'defaultOrder' => [
                    'position' => SORT_DESC,
                ],
            ]),
            'pagination' => ['pageSize' => 8],
        ]);
    }
}
