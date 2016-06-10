<?php

namespace common\models;

use common\models\tools\Visibility;
use Yii;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "description".
 *
 * @property string $description_id
 * @property string $description_pack_id
 * @property string $title
 * @property string $code
 * @property string $public_text
 * @property string $private_text
 * @property string $lang
 * @property string $visibility
 * @property integer $position
 *
 * @property DescriptionPack $descriptionPack
 */
class Description extends \yii\db\ActiveRecord
{
    const TYPE_APPEARANCE = 'appearance';
    const TYPE_HISTORY = 'history';
    const TYPE_PERSONALITY = 'personality';
    const TYPE_WHO = 'who';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'description';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description_pack_id', 'position'], 'integer'],
            [['description_pack_id', 'title', 'code', 'public_text', 'private_text', 'lang', 'visibility'], 'required'],
            [['public_text', 'private_text'], 'string'],
            [['title'], 'string', 'max' => 80],
            [['code'], 'string', 'max' => 40],
            [['lang'], 'string', 'max' => 8],
            [['visibility'], 'string', 'max' => 20],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::className(),
                'targetAttribute' => ['description_pack_id' => 'description_pack_id']
            ],
            [
                ['code'],
                'in',
                'range' => function () {
                    return $this->allowedTypes();
                }
            ],
            [
                ['visibility'],
                'in',
                'range' => function () {
                    return Visibility::allowedVisibilities();
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'description_id' => Yii::t('app', 'DESCRIPTION_ID'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
            'title' => Yii::t('app', 'DESCRIPTION_TITLE'),
            'code' => Yii::t('app', 'DESCRIPTION_CODE'),
            'public_text' => Yii::t('app', 'DESCRIPTION_TEXT_PUBLIC'),
            'private_text' => Yii::t('app', 'DESCRIPTION_TEXT_PRIVATE'),
            'lang' => Yii::t('app', 'LABEL_LANGUAGE'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
        ];
    }

    public function behaviors()
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
            ],
        ];
    }

    /**
     * @return string[]
     */
    static public function typeNames()
    {
        return [
            self::TYPE_APPEARANCE => Yii::t('app', 'DESCRIPTION_TYPE_APPEARANCE'),
            self::TYPE_HISTORY => Yii::t('app', 'DESCRIPTION_TYPE_HISTORY'),
            self::TYPE_PERSONALITY => Yii::t('app', 'DESCRIPTION_TYPE_PERSONALITY'),
            self::TYPE_WHO => Yii::t('app', 'DESCRIPTION_TYPE_WHO'),
        ];
    }

    static public function typesForCharacter()
    {
        return [self::TYPE_PERSONALITY];
    }

    static public function typesForPerson()
    {
        return [self::TYPE_APPEARANCE, self::TYPE_HISTORY, self::TYPE_WHO];
    }

    /**
     * @return string[]
     */
    public function allowedTypes()
    {
        return array_keys(self::typeNames());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDescriptionPack()
    {
        return $this->hasOne(DescriptionPack::className(), ['description_pack_id' => 'description_pack_id']);
    }
}
