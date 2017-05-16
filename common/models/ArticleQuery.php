<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Article;

/**
 * ArticleQuery represents the model behind the search form about `common\models\Article`.
 */
class ArticleQuery extends Article
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'epic_id'], 'integer'],
            [['key', 'title', 'subtitle', 'visibility', 'text_raw', 'text_ready'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Article::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
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
}
