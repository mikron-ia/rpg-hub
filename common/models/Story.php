<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\core\HasParameters;
use common\models\core\HasSightings;
use common\models\core\HasVisibility;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
 *
 * @property Epic $epic
 * @property ParameterPack $parameterPack
 * @property SeenPack $seenPack
 */
class Story extends ActiveRecord implements Displayable, HasParameters, HasEpicControl, HasSightings, HasVisibility
{
    use ToolsForEntity;

    const TYPE_CHAPTER = 'chapter';         // Part of larger story; interactive
    const TYPE_EPISODE = 'episode';         // Self-standing episode; interactive
    const TYPE_MISSION = 'mission';         // Self-standing episode; interactive
    const TYPE_PROLOGUE = 'prologue';       // Prologue to an arc; interactive or not
    const TYPE_INTERLUDE = 'interlude';     // Interlude in an arc; rarely interactive
    const TYPE_EPILOGUE = 'epilogue';       // Epilogue to an arc; interactive or not
    const TYPE_PART = 'part';               // Part of larger story; interactive
    const TYPE_READING = 'reading';         // Narration to give context; not interactive

    /**
     * @var string[]
     */
    private $parametersFormatted;

    public static function tableName()
    {
        return 'story';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name', 'short'], 'required'],
            [['epic_id', 'position'], 'integer'],
            [['short', 'long'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name'], 'string', 'max' => 120],
            [['visibility'], 'string', 'max' => 20],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['parameter_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ParameterPack::className(),
                'targetAttribute' => ['parameter_pack_id' => 'parameter_pack_id']
            ],
        ];
    }

    public function attributeLabels()
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
        ];
    }

    public function afterFind()
    {
        if ($this->seen_pack_id) {
            $this->seenPack->recordNotification();
        }
        parent::afterFind();
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->seenPack->updateRecord();
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert)
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

        return parent::beforeSave($insert);
    }

    public function behaviors()
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => ['epic_id'],
            ],
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::className(),
                'idName' => 'story_id',
                'className' => 'Story',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getParameterPack(): ActiveQuery
    {
        return $this->hasOne(ParameterPack::className(), ['parameter_pack_id' => 'parameter_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSeenPack(): ActiveQuery
    {
        return $this->hasOne(SeenPack::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * Provides story summary formatted in HTML
     * @return string Short summary formatted to HTML
     */
    public function getShortFormatted(): string
    {
        return Markdown::process($this->short, 'gfm');
    }

    /**
     * Provides story summary formatted in HTML
     * @return string Long summary formatted to HTML
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
                if ($parameter->visibility == Visibility::VISIBILITY_FULL) {
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

    public function getSimpleDataForApi()
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
            'parameters' => $this->formatParameters(),
            'short' => $this->getShortFormatted(),
        ];
    }

    public function getCompleteDataForApi()
    {

        $basicData = [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
            'parameters' => $this->formatParameters(),
            'short' => $this->getShortFormatted(),
            'long' => $this->getLongFormatted(),
        ];
        return $basicData;
    }

    public function isVisibleInApi()
    {
        return ($this->visibility === Visibility::VISIBILITY_FULL);
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

    static function throwExceptionAboutCreate()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_STORY'));
    }

    static function throwExceptionAboutControl()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_STORY'));
    }

    static function throwExceptionAboutIndex()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_STORY'));
    }

    static function throwExceptionAboutView()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_STORY'));
    }

    /**
     * @param string $parameterName
     * @return string
     */
    public function getParameter(string $parameterName): string
    {
        if (!$this->parametersFormatted) {
            $this->formatParameters();
        }

        if (isset($this->parametersFormatted[$parameterName])) {
            return $this->parametersFormatted[$parameterName]['value'];
        } else {
            return '';
        }
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

    public function getLongDescriptionWordCount()
    {
        return StringHelper::countWords($this->long);
    }

    /**
     * @return string[]
     */
    static public function typeNames(): array
    {
        return [
            self::TYPE_CHAPTER => Yii::t('app', 'STORY_TYPE_CHAPTER'),
            self::TYPE_PART => Yii::t('app', 'STORY_TYPE_PART'),
            self::TYPE_PROLOGUE => Yii::t('app', 'STORY_TYPE_PROLOGUE'),
            self::TYPE_INTERLUDE => Yii::t('app', 'STORY_TYPE_INTERLUDE'),
            self::TYPE_EPILOGUE => Yii::t('app', 'STORY_TYPE_EPILOGUE'),
            self::TYPE_EPISODE => Yii::t('app', 'STORY_TYPE_EPISODE'),
            self::TYPE_MISSION => Yii::t('app', 'STORY_TYPE_MISSION'),
            self::TYPE_READING => Yii::t('app', 'STORY_TYPE_READING'),
        ];
    }
}
