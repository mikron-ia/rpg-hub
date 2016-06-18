<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "story_parameter".
 *
 * @property string $story_parameter_id
 * @property string $story_id
 * @property string $code
 * @property string $visibility
 * @property string $content
 *
 * @property Story $story
 */
class StoryParameter extends \yii\db\ActiveRecord
{
    const PARAMETER_STORY_NUMBER = 'story-number';
    const PARAMETER_TIME_RANGE = 'time-range';
    const PARAMETER_POINT_START = 'point-start';
    const PARAMETER_POINT_END = 'point-end';
    const PARAMETER_SESSION_COUNT = 'session-count';
    const PARAMETER_XP_PARTY = 'party-xp';
    const PARAMETER_PCS_ACTIVE = 'active-pcs';
    const PARAMETER_CS_ACTIVE = 'active-cs';

    const VISIBILITY_NONE = 'none';
    const VISIBILITY_LOGGED = 'logged';
    const VISIBILITY_GM = 'gm';
    const VISIBILITY_FULL = 'full';

    private $allowedCodes = [
        self::PARAMETER_STORY_NUMBER,
        self::PARAMETER_TIME_RANGE,
        self::PARAMETER_POINT_START,
        self::PARAMETER_POINT_END,
        self::PARAMETER_SESSION_COUNT,
        self::PARAMETER_XP_PARTY,
        self::PARAMETER_PCS_ACTIVE,
        self::PARAMETER_CS_ACTIVE,
    ];

    private $allowedVisibility = [
        self::VISIBILITY_NONE,
        self::VISIBILITY_LOGGED,
        self::VISIBILITY_GM,
        self::VISIBILITY_FULL,
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'story_parameter';
    }

    static public function codeNames()
    {
        return [
            self::PARAMETER_STORY_NUMBER => Yii::t('app', 'ST_PARAM_STORY_NUMBER'),
            self::PARAMETER_TIME_RANGE => Yii::t('app', 'ST_PARAM_TIME_RANGE'),
            self::PARAMETER_POINT_START => Yii::t('app', 'ST_PARAM_POINT_START'),
            self::PARAMETER_POINT_END => Yii::t('app', 'ST_PARAM_POINT_END'),
            self::PARAMETER_SESSION_COUNT => Yii::t('app', 'ST_PARAM_SESSION_COUNT'),
            self::PARAMETER_XP_PARTY => Yii::t('app', 'ST_PARAM_XP_PARTY'),
            self::PARAMETER_PCS_ACTIVE => Yii::t('app', 'ST_PARAM_PCS_ACTIVE'),
            self::PARAMETER_CS_ACTIVE => Yii::t('app', 'ST_PARAM_CS_ACTIVE'),
        ];
    }

    static public function visibilityNames()
    {
        return [
            self::VISIBILITY_NONE => Yii::t('app', 'VISIBILITY_NONE'),
            self::VISIBILITY_LOGGED => Yii::t('app', 'VISIBILITY_LOGGED'),
            self::VISIBILITY_GM => Yii::t('app', 'VISIBILITY_GM'),
            self::VISIBILITY_FULL => Yii::t('app', 'VISIBILITY_FULL'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'code', 'content'], 'required'],
            [['story_id'], 'integer'],
            [['visibility'], 'string'],
            [['code'], 'string', 'max' => 20],
            [['content'], 'string', 'max' => 80],
            [
                ['story_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Story::className(),
                'targetAttribute' => ['story_id' => 'story_id']
            ],
            [
                ['code'],
                'in',
                'range' => function ($model, $attribute) {
                    return $this->allowedCodes;
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
            'story_parameter_id' => Yii::t('app', 'STORY_PARAMETER_ID'),
            'story_id' => Yii::t('app', 'STORY_ID'),
            'code' => Yii::t('app', 'STORY_PARAMETER_CODE'),
            'visibility' => Yii::t('app', 'STORY_PARAMETER_VISIBILITY'),
            'content' => Yii::t('app', 'STORY_PARAMETER_CONTENT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::className(), ['story_id' => 'story_id']);
    }

    /**
     * @inheritdoc
     * @return StoryParameterQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StoryParameterQuery(get_called_class());
    }

    public function getCodeName()
    {
        $codes = self::codeNames();
        return isset($codes[$this->code]) ? $codes[$this->code] : $this->code;
    }

    public function getVisibilityName()
    {
        $visibilities = self::visibilityNames();
        return isset($visibilities[$this->visibility]) ? $visibilities[$this->visibility] : $this->visibility;
    }
}
