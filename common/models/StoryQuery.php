<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Story;

/**
 * StoryQuery represents the model behind the search form about `common\models\Story`.
 */
class StoryQuery extends Story
{
    public $descriptions;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id'], 'integer'],
            [['descriptions'], 'string'],
            [['key', 'name', 'short', 'long', 'data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();

        $attributeLabels['descriptions'] = Yii::t('app', 'STORY_DESCRIPTIONS');

        return $attributeLabels;
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
        $query = Story::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere([
                'or',
                ['like', 'short', $this->descriptions],
                ['like', 'long', $this->descriptions]
            ]);

        return $dataProvider;
    }
}
