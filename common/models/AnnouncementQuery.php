<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\db\conditions\AndCondition;
use yii\db\conditions\BetweenColumnsCondition;
use yii\db\conditions\OrCondition;
use yii\db\conditions\SimpleCondition;

/**
 * AnnouncementQuery represents the model behind the search form of `common\models\Announcement`.
 */
class AnnouncementQuery extends Announcement
{
    private const int DEFAULT_FRONT_LIMIT = 4;
    private const int DEFAULT_LIST_LIMIT = 8;

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

    public function mostRecentDataProvider(?Epic $activeEpic = null, bool $limitByTime = true): ActiveDataProvider
    {
        if (empty($activeEpic)) {
            $activeEpic = Yii::$app->params['activeEpic'];
        }

        $query = Announcement::find()
            ->where(new SimpleCondition('epic_id', '=', $activeEpic->epic_id))
            ->orWhere(new SimpleCondition('epic_id', 'IS', null));

        if ($limitByTime) {
            $query = $this->limitByTime($query);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['announcement_id' => SORT_DESC]],
            'pagination' => false,
        ]);

        $query->limit(self::DEFAULT_FRONT_LIMIT);

        return $dataProvider;
    }

    public function mostRecentByPlayerDataProvider(array $userIds): ArrayDataProvider
    {
        $query = Announcement::find()
            ->where(['in', 'epic_id', $userIds])
            ->orderBy(['announcement_id' => SORT_DESC, 'visible_from' => SORT_DESC]);

        $query = $this->limitByTime($query);

        $mostRecentAnnouncements = [];

        foreach ($query->all() as $announcement) {
            /** @var Announcement $announcement */
            if (!isset($mostRecentAnnouncements[$announcement->epic_id])) {
                $mostRecentAnnouncements[$announcement->epic_id] = $announcement;
            }
        }

        return new ArrayDataProvider(['allModels' => $mostRecentAnnouncements, 'pagination' => false]);
    }

    /**
     * Creates a data provider instance with the search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params, bool $limitByTime = true): ActiveDataProvider
    {
        $query = Announcement::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere(['epic_id' => Yii::$app->params['activeEpic']->epic_id]);
        }

        if ($limitByTime) {
            $query = $this->limitByTime($query);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => self::DEFAULT_LIST_LIMIT],
            'sort' => ['defaultOrder' => ['visible_from' => SORT_DESC, 'announcement_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'text_ready', $this->text_ready]);

        return $dataProvider;
    }

    private function limitByTime(ActiveQuery $query): ActiveQuery
    {
        return $query->andWhere(new OrCondition([
            new BetweenColumnsCondition(time(), 'BETWEEN', 'visible_from', 'visible_to'),
            new AndCondition([
                new SimpleCondition('visible_from', '<', time()),
                new SimpleCondition('visible_to', 'IS', null),
            ])
        ]));
    }
}
