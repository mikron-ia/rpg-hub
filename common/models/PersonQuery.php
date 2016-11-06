<?php

namespace common\models;

use common\models\core\Visibility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PersonQuery represents the model behind the search form about `common\models\Person`.
 */
final class PersonQuery extends Person
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_id', 'character_id'], 'integer'],
            [['epic_id', 'name', 'tagline', 'visibility'], 'safe'],
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
        $query = Person::find();

        // add conditions that should always apply here

        if (empty(Yii::$app->params['activeEpic'])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'ERROR_NO_EPIC_ACTIVE'));
            $query->where('0=1');
        } else {
            $visibilityVector = [Visibility::VISIBILITY_FULL, Visibility::VISIBILITY_LOGGED];

            if (Participant::participantHasRole(
                Yii::$app->user->identity,
                Yii::$app->params['activeEpic'],
                ParticipantRole::ROLE_GM
            )
            ) {
                $visibilityVector[] = Visibility::VISIBILITY_GM;
                $visibilityVector[] = Visibility::VISIBILITY_DESIGNATED;
            }

            {
                $query->andWhere([
                    'epic_id' => Yii::$app->params['activeEpic']->epic_id,
                    'visibility' => $visibilityVector,
                ]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 24],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'person_id' => $this->person_id,
            'epic_id' => $this->epic_id,
            'character_id' => $this->character_id,
        ]);

        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'tagline', $this->tagline])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['in', 'visibility', $this->visibility]);

        return $dataProvider;
    }
}
