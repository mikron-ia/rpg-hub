<?php

namespace common\models;

use common\models\core\Language;
use common\models\core\Visibility;
use Yii;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "parameter".
 *
 * @property string $parameter_id
 * @property string $parameter_pack_id
 * @property string $code
 * @property string $lang
 * @property string $visibility
 * @property integer $position
 * @property string $content
 *
 * @property ParameterPack $parameterPack
 */
class Parameter extends \yii\db\ActiveRecord
{
    const STORY_NUMBER = 'story-number';
    const TIME_RANGE = 'time-range';
    const LOCATION_POINT_START = 'point-start';
    const LOCATION_POINT_END = 'point-end';
    const SESSION_COUNT = 'session-count';
    const XP_PARTY = 'party-xp';
    const PCS_ACTIVE = 'active-pcs';
    const CS_ACTIVE = 'active-cs';
    const DATA_SOURCE_FOR_REPUTATION = 'source-reputation';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parameter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parameter_pack_id', 'position'], 'integer'],
            [['code', 'content'], 'required'],
            [['code', 'visibility'], 'string', 'max' => 20],
            [['lang'], 'string', 'max' => 5],
            [['content'], 'string', 'max' => 80],
            [
                ['parameter_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ParameterPack::className(),
                'targetAttribute' => ['parameter_pack_id' => 'parameter_pack_id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parameter_id' => Yii::t('app', 'PARAMETER_ID'),
            'parameter_pack_id' => Yii::t('app', 'PARAMETER_PACK'),
            'code' => Yii::t('app', 'PARAMETER_CODE'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'position' => Yii::t('app', 'LABEL_POSITION'),
            'content' => Yii::t('app', 'PARAMETER_CONTENT'),
        ];
    }

    public function behaviors()
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => ['parameter_pack_id'],
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function allowedTypes()
    {
        return array_keys(self::typeNames());
    }

    /**
     * @return string[]
     */
    static public function typeNames()
    {
        return [
            self::STORY_NUMBER => Yii::t('app', 'ST_PARAM_STORY_NUMBER'),
            self::TIME_RANGE => Yii::t('app', 'ST_PARAM_TIME_RANGE'),
            self::LOCATION_POINT_START => Yii::t('app', 'ST_PARAM_POINT_START'),
            self::LOCATION_POINT_END => Yii::t('app', 'ST_PARAM_POINT_END'),
            self::SESSION_COUNT => Yii::t('app', 'ST_PARAM_SESSION_COUNT'),
            self::XP_PARTY => Yii::t('app', 'ST_PARAM_XP_PARTY'),
            self::PCS_ACTIVE => Yii::t('app', 'ST_PARAM_PCS_ACTIVE'),
            self::CS_ACTIVE => Yii::t('app', 'ST_PARAM_CS_ACTIVE'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParameterPack()
    {
        return $this->hasOne(ParameterPack::className(), ['parameter_pack_id' => 'parameter_pack_id']);
    }/**
 * @return string|null
 */
    public function getTypeName()
    {
        $names = self::typeNames();
        if (isset($names[$this->code])) {
            return $names[$this->code];
        } else {
            return "?";
        }
    }

    public function getLanguage()
    {
        $language = Language::create($this->lang);
        return $language->getName();
    }

    public function getVisibility()
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getName();
    }

    public function getVisibilityLowercase()
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getNameLowercase();
    }

    public function getCodeName()
    {
        $codes = self::typeNames();
        return isset($codes[$this->code]) ? $codes[$this->code] : $this->code;
    }

    public function getVisibilityName()
    {
        $visibilities = Visibility::visibilityNames();
        return isset($visibilities[$this->visibility]) ? $visibilities[$this->visibility] : $this->visibility;
    }
}
