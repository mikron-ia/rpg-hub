<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\conditions\BetweenColumnsCondition;
use yii\db\conditions\SimpleCondition;

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

    public function mostRecentDataProvider(Epic $activeEpic = null, bool $limitByTime = true): ActiveDataProvider
    {
        if (empty($activeEpic)) {
            $activeEpic = Yii::$app->params['activeEpic'];
        }

        $query = Announcement::find()
            ->where(new SimpleCondition('epic_id', '=', $activeEpic->epic_id))
            ->orWhere(new SimpleCondition('epic_id', 'IS', null));

        if ($limitByTime) {
            $query->andWhere(new BetweenColumnsCondition(time(), 'BETWEEN', 'visible_from', 'visible_to'));
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['announcement_id' => SORT_DESC]],
            'pagination' => false,
        ]);

        $query->limit(4);

        return $dataProvider;
    }

    public function mostRecentByPlayerDataProvider(array $userIds): ArrayDataProvider
    {
        $query = Announcement::find()->where(['in', 'epic_id', $userIds])
            ->andWhere(new BetweenColumnsCondition(time(), 'BETWEEN', 'visible_from', 'visible_to'))
            ->orderBy(['announcement_id' => SORT_DESC, 'visible_from' => SORT_DESC]);

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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search(array $params, ?string $formName = null): ActiveDataProvider
    {
        $query = Announcement::find();

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $query->andWhere(['epic_id' => Yii::$app->params['activeEpic']->epic_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['visible_from' => SORT_DESC, 'announcement_id' => SORT_DESC]],
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
