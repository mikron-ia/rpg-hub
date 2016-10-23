<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PerformedAction;

/**
 * PerformedActionQuery represents the model behind the search form about `common\models\PerformedAction`.
 */
class PerformedActionQuery extends PerformedAction
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'object_id', 'performed_at'], 'integer'],
            [['operation', 'class'], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params):ActiveDataProvider
    {
        $query = PerformedAction::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 16],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'object_id' => $this->object_id,
            'performed_at' => $this->performed_at,
        ]);

        $query->andFilterWhere(['like', 'operation', $this->operation])
            ->andFilterWhere(['like', 'class', $this->class]);

        return $dataProvider;
    }
}
