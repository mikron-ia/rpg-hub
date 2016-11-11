<?php

namespace common\models;

use common\models\core\Language;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * DescriptionQuery represents the model behind the search form about `common\models\Description`.
 */
final class DescriptionQuery extends Description
{
    public $text;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description_pack_id'], 'integer'],
            [['code', 'public_text', 'private_text', 'lang', 'visibility'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();

        $attributeLabels['text'] = Yii::t('app', 'DESCRIPTION_TEXTS');

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
        $query = Description::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'description_pack_id' => $this->description_pack_id,
        ]);

        $query->andFilterWhere(['in', 'code', $this->code])
            ->andFilterWhere(['in', 'lang', $this->lang])
            ->andFilterWhere(['in', 'visibility', $this->visibility])
            ->andFilterWhere([
                'or',
                ['like', 'public_text', $this->public_text],
                ['like', 'public_text', $this->text],
                ['like', 'private_text', $this->text]
            ]);;

        return $dataProvider;
    }

    /**
     * @param int $description_pack_id
     * @param Language $language
     * @return ActiveQuery
     */
    static public function listDescriptionsInLanguage(int $description_pack_id, Language $language):ActiveQuery
    {
        $query = Description::find();

        $query->andWhere([
            'description_pack_id' => $description_pack_id,
            'lang' => $language->language,
        ]);

        return $query;
    }
}
