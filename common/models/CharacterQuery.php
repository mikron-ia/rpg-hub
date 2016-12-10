<?php

namespace common\models;

use common\models\core\Visibility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CharacterQuery represents the model behind the search form about `common\models\Character`.
 */
final class CharacterQuery extends Character
{
    /**
     * @var int
     */
    private $pageCount;

    public function __construct($pagination = 24, array $config = [])
    {
        $this->pageCount = $pagination;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['character_id', 'character_sheet_id'], 'integer'],
            [['epic_id', 'name', 'tagline', 'visibility'], 'safe'],
        ];
    }

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
        $query = Character::find()->joinWith('seenPack', true, 'LEFT JOIN');

        // add conditions that should always apply here

        if (empty(Yii::$app->params['activeEpic'])) {
            $query->where('0=1');
        }

        $query->andWhere([
            'epic_id' => Yii::$app->params['activeEpic']->epic_id,
            'visibility' => Visibility::determineVisibilityVector(),
        ]);

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
            'character_id' => $this->character_id,
            'epic_id' => $this->epic_id,
            'character_sheet_id' => $this->character_sheet_id,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'tagline', $this->tagline])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['in', 'visibility', $this->visibility]);

        return $dataProvider;
    }
}
