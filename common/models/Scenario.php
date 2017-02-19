<?php

namespace common\models;

use common\models\core\HasDescriptions;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "scenario".
 *
 * @property string $scenario_id
 * @property string $epic_id
 * @property string $name
 * @property string $tag_line
 * @property string $description_pack_id
 *
 * @property DescriptionPack $descriptionPack
 * @property Epic $epic
 */
class Scenario extends ActiveRecord implements HasDescriptions
{
    public static function tableName()
    {
        return 'scenario';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name'], 'required'],
            [['epic_id', 'description_pack_id'], 'integer'],
            [['name'], 'string', 'max' => 120],
            [['tag_line'], 'string', 'max' => 255],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::className(),
                'targetAttribute' => ['description_pack_id' => 'description_pack_id']
            ],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'scenario_id' => Yii::t('app', 'SCENARIO_ID'),
            'epic_id' => Yii::t('app', 'EPIC_ID'),
            'name' => Yii::t('app', 'SCENARIO_NAME'),
            'tag_line' => Yii::t('app', 'SCENARIO_TAGLINE'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK_ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDescriptionPack()
    {
        return $this->hasOne(DescriptionPack::className(), ['description_pack_id' => 'description_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    static public function allowedDescriptionTypes():array
    {
        return [];
    }
}
