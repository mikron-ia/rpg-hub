<?php

namespace common\models;

use Override;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SecretQuery extends Secret
{
    #[Override]
    public function rules(): array
    {
        return [
            [['title', 'content', 'notes'], 'safe'],
        ];
    }

    #[Override]
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Secret::find()->with('bestowedList', 'bestowedList.bestowed', 'bestowedList.bestowed.user');

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere(['epic_id' => Yii::$app->params['activeEpic']->epic_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['updated_at' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'note', $this->content])
            ->andFilterWhere(['like', 'note', $this->notes]);

        return $dataProvider;
    }
}
