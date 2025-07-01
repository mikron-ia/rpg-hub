<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AnnouncementQuery represents the model behind the search form of `common\models\Announcement`.
 */
class AnnouncementQuery extends Announcement
{
    public function rules(): array
    {
        return [
            [['announcement_id', 'epic_id'], 'integer'],
            [['title', 'text_ready'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function mostRecentDataProvider(): ?ActiveDataProvider
    {
        $query = Announcement::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['announcement_id' => SORT_DESC]],
            'pagination' => false,
        ]);

        $query->limit(4);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search(array $params, ?string $formName = null): ActiveDataProvider
    {
        $query = Announcement::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['announcement_id' => SORT_DESC]],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'text_ready', $this->text_ready]);

        return $dataProvider;
    }
}
