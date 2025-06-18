<?php

namespace common\models;

use common\models\core\Language;
use common\models\core\Visibility;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * DescriptionQuery represents the model behind the search form about `common\models\Description`.
 */
final class DescriptionQuery extends Description
{
    public ?string $text;

    public function rules(): array
    {
        return [
            [['description_pack_id'], 'integer'],
            [['code', 'public_text', 'private_text', 'visibility'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        $attributeLabels = parent::attributeLabels();

        $attributeLabels['text'] = Yii::t('app', 'DESCRIPTION_TEXTS');

        return $attributeLabels;
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Description::find();

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['description_pack_id' => $this->description_pack_id]);

        $query->andFilterWhere(['in', 'code', $this->code])
            ->andFilterWhere(['in', 'visibility', $this->visibility])
            ->andFilterWhere([
                'or',
                ['like', 'public_text', $this->public_text],
                ['like', 'public_text', $this->text],
                ['like', 'private_text', $this->text],
            ]);

        return $dataProvider;
    }

    static public function listDescriptionsInLanguage(DescriptionPack $descriptionPack, Language $language): ActiveQuery
    {
        $query = Description::find();

        $query->andWhere([
            'description_pack_id' => $descriptionPack->description_pack_id,
            'lang' => $language->language,
            'visibility' => Visibility::determineVisibilityVector($descriptionPack->epic),
        ]);

        return $query;
    }

    static public function listDescriptions(DescriptionPack $descriptionPack): ActiveQuery
    {
        $query = Description::find();

        $query->andWhere([
            'description_pack_id' => $descriptionPack->description_pack_id,
            'visibility' => Visibility::determineVisibilityVector($descriptionPack->epic),
        ]);

        return $query;
    }
}
