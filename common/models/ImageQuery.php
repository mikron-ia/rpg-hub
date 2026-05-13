<?php

namespace common\models;

use Override;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ImageQuery extends Image
{
    #[Override]
    public function rules(): array
    {
        return [
            [['name', 'title', 'alt', 'note'], 'safe'],
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
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Image::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere(['epic_id' => Yii::$app->params['activeEpic']->epic_id]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['updated_at' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'alt', $this->alt])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
