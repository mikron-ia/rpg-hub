<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\core\HasParameters;
use common\models\core\HasSightings;
use common\models\core\HasVisibility;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasVisibility;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\helpers\StringHelper;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "story".
 *
 * @property string $story_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $short
 * @property string $long
 * @property int $position
 * @property string $visibility
 * @property string $data
 * @property string $parameter_pack_id
 * @property string $seen_pack_id
 * @property string $utility_bag_id
 *
 * @property Epic $epic
 * @property ParameterPack $parameterPack
 * @property SeenPack $seenPack
 * @property UtilityBag $utilityBag
 */
class Story extends ActiveRecord implements Displayable, HasParameters, HasEpicControl, HasSightings, HasVisibility
{
    use ToolsForEntity;
    use ToolsForHasVisibility;

    /**
     * @var array<string,string>
     */
    private array $parametersFormatted = [];

    public bool $is_off_the_record_change = false;

    public static function tableName(): string
    {
        return 'story';
    }

    public function rules(): array
    {
        return [
            [['epic_id', 'name', 'short'], 'required'],
            [['epic_id', 'position'], 'integer'],
            [['short', 'long'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name'], 'string', 'max' => 120],
            [['visibility'], 'string', 'max' => 20],
            [['is_off_the_record_change'], 'boolean'],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['parameter_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ParameterPack::class,
                'targetAttribute' => ['parameter_pack_id' => 'parameter_pack_id']
            ],
            [
                ['visibility'],
                'in',
                'range' => fn() => $this->allowedVisibilitiesForValidator(),
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'story_id' => Yii::t('app', 'STORY_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'STORY_KEY'),
            'name' => Yii::t('app', 'STORY_NAME'),
            'short' => Yii::t('app', 'STORY_SHORT'),
            'long' => Yii::t('app', 'STORY_LONG'),
            'position' => Yii::t('app', 'STORY_POSITION'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'data' => Yii::t('app', 'STORY_DATA'),
            'parameter_pack_id' => Yii::t('app', 'PARAMETER_PACK'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
            'is_off_the_record_change' => Yii::t('app', 'CHECK_OFF_THE_RECORD_CHANGE'),
        ];
    }

    public function afterFind(): void
    {
        if ($this->seen_pack_id) {
            $this->seenPack->recordNotification();
        }
        parent::afterFind();
    }

    public function afterSave($insert, $changedAttributes): void
    {
        if (!$this->is_off_the_record_change) {
            $this->seenPack->updateRecord();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
            $this->data = json_encode([]);
        }

        if (empty($this->parameter_pack_id)) {
            $pack = ParameterPack::create('Story');
            $this->parameter_pack_id = $pack->parameter_pack_id;
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('Story');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('Story');
            $this->utility_bag_id = $pack->utility_bag_id;
        }

        return parent::beforeSave($insert);
    }

    public function behaviors(): array
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::class,
                'positionAttribute' => 'position',
                'groupAttributes' => ['epic_id'],
            ],
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'story_id',
                'className' => 'Story',
            ],
        ];
    }

    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    public function getParameterPack(): ActiveQuery
    {
        return $this->hasOne(ParameterPack::class, ['parameter_pack_id' => 'parameter_pack_id']);
    }

    public function getSeenPack(): ActiveQuery
    {
        return $this->hasOne(SeenPack::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getUtilityBag(): ActiveQuery
    {
        return $this->hasOne(UtilityBag::class, ['utility_bag_id' => 'utility_bag_id']);
    }

    /**
     * Provides story summary formatted in HTML
     */
    public function getShortFormatted(): string
    {
        return Markdown::process($this->short, 'gfm');
    }

    /**
     * Provides story summary formatted in HTML
     */
    public function getLongFormatted(): string
    {
        return Markdown::process($this->long, 'gfm');
    }

    /**
     * @return array
     * @todo Consider parameter visibility based on access rights - vide issue #104
     */
    public function formatParameters(): array
    {
        if (!$this->parametersFormatted) {
            $parameters = [];

            foreach ($this->parameterPack->parameters as $parameter) {
                if ($parameter->getVisibility() === Visibility::VISIBILITY_FULL) {
                    $parameters[$parameter->code] = [
                        'name' => $parameter->getCodeName(),
                        'value' => $parameter->content,
                    ];
                }
            }

            $this->parametersFormatted = $parameters;
        }

        return $this->parametersFormatted;
    }

    /**
     * @return array<string,string>
     */
    public function getSimpleDataForApi(): array
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
            'parameters' => $this->formatParameters(),
            'short' => $this->getShortFormatted(),
        ];
    }

    /**
     * @return array<string,string|array>
     */
    public function getCompleteDataForApi(): array
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
            'parameters' => $this->formatParameters(),
            'short' => $this->getShortFormatted(),
            'long' => $this->getLongFormatted(),
        ];
    }

    public function isVisibleInApi(): bool
    {
        return ($this->getVisibility() === Visibility::VISIBILITY_FULL);
    }

    static public function allowedParameterTypes(): array
    {
        return [
            Parameter::STORY_NUMBER,
            Parameter::TIME_RANGE,
            Parameter::LOCATION_POINT_START,
            Parameter::LOCATION_POINT_END,
            Parameter::SESSION_COUNT,
            Parameter::XP_PARTY,
            Parameter::PCS_ACTIVE,
            Parameter::CS_ACTIVE,
        ];
    }

    static public function availableParameterTypes(): array
    {
        return [
            Parameter::STORY_NUMBER,
            Parameter::TIME_RANGE,
            Parameter::LOCATION_POINT_START,
            Parameter::LOCATION_POINT_END,
            Parameter::SESSION_COUNT,
            Parameter::XP_PARTY,
            Parameter::PCS_ACTIVE,
            Parameter::CS_ACTIVE,
        ];
    }

    static public function canUserIndexThem(): bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    static public function canUserCreateThem(): bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    public function canUserControlYou(): bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    public function canUserViewYou(): bool
    {
        return self::canUserViewInEpic($this->epic);
    }

    static function throwExceptionAboutCreate(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_STORY'));
    }

    static function throwExceptionAboutControl(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_STORY'));
    }

    static function throwExceptionAboutIndex(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_STORY'));
    }

    static function throwExceptionAboutView(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_STORY'));
    }

    public function getParameter(string $parameterName): string
    {
        if (!$this->parametersFormatted) {
            $this->formatParameters();
        }

        if (isset($this->parametersFormatted[$parameterName])) {
            return $this->parametersFormatted[$parameterName]['value'];
        }

        return '';
    }

    public function recordSighting(): bool
    {
        return $this->seenPack->recordSighting();
    }

    public function recordNotification(): bool
    {
        return $this->seenPack->recordNotification();
    }

    public function showSightingStatus(): string
    {
        return $this->seenPack->getStatusForCurrentUser();
    }

    public function showSightingCSS(): string
    {
        return $this->seenPack->getCSSForCurrentUser();
    }

    public function getLongDescriptionWordCount(): int
    {
        return StringHelper::countWords($this->long);
    }

    public function __toString()
    {
        return Html::a($this->name, ['story/view', 'key' => $this->key]);
    }
}
