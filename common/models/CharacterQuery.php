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
            'sort' => ['defaultOrder' => ['importance_category' => SORT_ASC]],
            'pagination' => ['pageSize' => $this->pageCount],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'tagline', $this->tagline])
            ->andFilterWhere(['in', 'visibility', $this->visibility]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied and applies default order according to importance
     * This list is more suitable for front section
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchForUser($params):ActiveDataProvider
    {
        $search = $this->search($params);

        $search->sort = ['defaultOrder' => ['importance_category' => SORT_ASC]];

        return $search;
    }

    /**
     * Creates data provider instance with search query applied and applies default order according to time of the last modification
     * This list is more suitable for operator section
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchForOperator($params):ActiveDataProvider
    {
        $search = $this->search($params);

        $search->sort = ['defaultOrder' => ['updated_at' => SORT_DESC]];

        return $search;
    }

    /**
     * @return string[]
     */
    static public function listEpicCharactersAsArray():array
    {
        $query = Character::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere([
                'epic_id' => Yii::$app->params['activeEpic']->epic_id,
                'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic']),
            ]);
        }

        $characters = $query->all();

        $arrayOfNames = [];

        foreach ($characters as $character) {
            /* @var $character Character */
            $arrayOfNames[$character->character_id] = $character->name;
        }

        return $arrayOfNames;
    }
}
