<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasVisibility;
use common\models\core\Language;
use common\models\core\Visibility;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
class Parameter extends ActiveRecord implements HasVisibility
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
    const EPIC_STATUS = 'epic-status';
    const EPIC_SYSTEM_STATE = 'epic-system-state';
    const LANGUAGE = 'language';

    public static function tableName(): string
    {
        return 'parameter';
    }

    public function rules(): array
    {
        return [
            [['parameter_pack_id', 'position'], 'integer'],
            [['code', 'content'], 'required'],
            [['code', 'visibility'], 'string', 'max' => 20],
            [['lang'], 'string', 'max' => 5],
            [['content'], 'string', 'max' => 120],
            [
                ['parameter_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ParameterPack::class,
                'targetAttribute' => ['parameter_pack_id' => 'parameter_pack_id']
            ],
        ];
    }

    public function attributeLabels(): array
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

    public function afterSave($insert, $changedAttributes): void
    {
        if (!empty($changedAttributes)) {
            $this->parameterPack->touch('updated_at');
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function behaviors(): array
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::class,
                'positionAttribute' => 'position',
                'groupAttributes' => ['parameter_pack_id'],
            ],
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'parameter_id',
                'className' => 'Parameter',
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'blameableBehavior' => [
                'class' => BlameableBehavior::class,
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function allowedTypes(): array
    {
        return array_keys(self::typeNames());
    }

    /**
     * @return string[]
     */
    static public function typeNames(): array
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
            self::DATA_SOURCE_FOR_REPUTATION => Yii::t('app', 'PARAM_DATA_SOURCE_FOR_REPUTATION'),
            self::EPIC_STATUS => Yii::t('app', 'PARAM_EPIC_STATUS'),
            self::EPIC_SYSTEM_STATE => Yii::t('app', 'PARAM_EPIC_SYSTEM_STATE'),
            self::LANGUAGE => Yii::t('app', 'PARAM_LANGUAGE'),
        ];
    }

    /**
     * @return string[]
     */
    public function typeNamesForThisClassForAdd(): array
    {
        return $this->typeNamesForThisClassPerMethod('availableParameterTypes');
    }

    /**
     * @return string[]
     */
    public function typeNamesForThisClassForEdit(): array
    {
        return $this->typeNamesForThisClassPerMethod('allowedParameterTypes');
    }

    /**
     * @param string $methodToUse
     *
     * @return string[]
     */
    private function typeNamesForThisClassPerMethod(string $methodToUse): array
    {
        $typeNamesAll = self::typeNames();
        $typeNamesAccepted = [];

        $class = 'common\models\\' . $this->parameterPack->class;

        if (method_exists($class, $methodToUse)) {
            $typesAllowed = call_user_func([$class, $methodToUse]);
        } else {
            $typesAllowed = array_keys($typeNamesAll);
        }

        foreach ($typeNamesAll as $typeKey => $typeName) {
            if (in_array($typeKey, $typesAllowed, true)) {
                $typeNamesAccepted[$typeKey] = $typeName;
            }
        }

        return $typeNamesAccepted;
    }

    public function getParameterPack(): ActiveQuery
    {
        return $this->hasOne(ParameterPack::class, ['parameter_pack_id' => 'parameter_pack_id']);
    }

    public function getTypeName(): string
    {
        $names = self::typeNames();
        if (isset($names[$this->code])) {
            return $names[$this->code];
        } else {
            return '?';
        }
    }

    /**
     * @return string|null Language name
     */
    public function getLanguage(): ?string
    {
        $language = Language::create($this->lang);
        return $language->getName();
    }

    static public function allowedVisibilities(): array
    {
        return [
            Visibility::VISIBILITY_GM,
            Visibility::VISIBILITY_FULL
        ];
    }

    public function getVisibility(): string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getName();
    }

    public function getVisibilityLowercase(): string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getNameLowercase();
    }

    /**
     * @return string Code name in chosen language
     */
    public function getCodeName(): string
    {
        $codes = self::typeNames();
        return isset($codes[$this->code]) ? $codes[$this->code] : $this->code;
    }

    /**
     * @return string Visibility name in chosen language
     */
    public function getVisibilityName(): string
    {
        $visibilities = Visibility::visibilityNames(self::allowedVisibilities());
        return isset($visibilities[$this->visibility]) ? $visibilities[$this->visibility] : $this->visibility;
    }
}
