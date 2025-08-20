<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasVisibility;
use common\models\core\Language;
use common\models\core\Visibility;
use common\models\tools\ToolsForHasVisibility;
use common\models\tools\ToolsForLinkTags;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "description".
 *
 * @property int $description_id
 * @property int $description_pack_id
 * @property string $title
 * @property string $code
 * @property string $public_text
 * @property string $protected_text
 * @property string $private_text
 * @property string $public_text_expanded
 * @property string $protected_text_expanded
 * @property string $private_text_expanded
 * @property string $lang
 * @property string $visibility
 * @property int $position
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int|null $point_in_time_start_id
 * @property int|null $point_in_time_end_id
 * @property int|null $point_in_time_still_valid_id
 *
 * @property User $createdBy
 * @property DescriptionPack $descriptionPack
 * @property User $updatedBy
 * @property PointInTime $pointInTimeStart
 * @property PointInTime $pointInTimeEnd
 * @property PointInTime $pointInTimeStillValid
 */
class Description extends ActiveRecord implements Displayable, HasVisibility
{
    use ToolsForLinkTags;
    use ToolsForHasVisibility;

    const TYPE_APPEARANCE = 'appearance';       // For Character; The looks
    const TYPE_ASPECTS = 'aspects';             // For Character, Group, Scenario, Story; Aspects (for FATE-like games) and Moves (for Powered by Apocalypse games)
    const TYPE_ATTITUDE = 'attitude';           // For Character, Group; Attitude towards different people / groups and connections with them
    const TYPE_BACKGROUND = 'background';       // For Character, Group, Story; Origin, education, the like
    const TYPE_COMMENTARY = 'commentary';       // For Character, Group, Story; GM commentary
    const TYPE_DOMAIN = 'domain';               // For Character, Group; Places where the person reigns, dominates, or frequents
    const TYPE_FAME = 'fame';                   // For Character; Famous deeds or events; REMOVED
    const TYPE_FACTIONS = 'factions';           // For Character, Group; Factions associated with; this includes nations
    const TYPE_HISTORY = 'history';             // For Character, Group; History of the person or group
    const TYPE_INTERACTIONS = 'interactions';   // For Character, Group; Interactions / encounters with the group or person NAMES
    const TYPE_PERSONALITY = 'personality';     // For Character; Personality, character behaviour, mental issues
    const TYPE_RESOURCES = 'resources';         // For Character, Group; Resources the person wields, flaunts, can offer
    const TYPE_REPUTATION = 'reputation';       // For Character; Character's reputation
    const TYPE_RETINUE = 'retinue';             // For Character, Group; Friends, allies, etc.
    const TYPE_RUMOURS = 'rumours';             // For Character, Group; Unproven rumours collected about character
    const TYPE_STORIES = 'stories';             // For Character, Group; Stories person participated in
    const TYPE_THREADS = 'threads';             // For Character, Group, Scenario, Story; Threads attached
    const TYPE_WHO = 'who';                     // For Character, Group; Who is this?

    const TYPE_STRUCTURE = 'structure';         // For Group: what is the structure and basic workings?

    const TYPE_PREMISE = 'premise';             // For Scenario, Story; what is the main concept?
    const TYPE_ACTORS = 'actors';               // For Scenario, Story; who is going to participate?
    const TYPE_PLAN = 'plan';                   // For Scenario; what is going to happen?
    const TYPE_SCENE = 'scene';                 // For Scenario, Story; a particular scene
    const TYPE_ACT = 'act';                     // For Scenario, Story; a particular act
    const TYPE_BRIEFING = 'briefing';           // For Scenario, Story; briefing / introduction scene
    const TYPE_DEBRIEFING = 'debriefing';       // For Scenario, Story; debriefing / aftermath scene
    const TYPE_PRELUDE = 'prelude';             // For Scenario, Story; events leading to or introducing
    const TYPE_INTERLUDE = 'interlude';         // For Scenario, Story; events in-between
    const TYPE_POSTLUDE = 'postlude';           // For Scenario, Story; events following

    public static function tableName(): string
    {
        return 'description';
    }

    public function rules(): array
    {
        return [
            [
                [
                    'description_pack_id',
                    'position',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'point_in_time_start_id',
                    'point_in_time_end_id',
                    'point_in_time_still_valid_id',
                ],
                'integer'
            ],
            [['description_pack_id', 'code', 'public_text', 'lang', 'visibility'], 'required'],
            [
                [
                    'public_text',
                    'protected_text',
                    'private_text',
                    'public_text_expanded',
                    'protected_text_expanded',
                    'private_text_expanded',
                ],
                'string'
            ],
            [['code'], 'string', 'max' => 40],
            [['lang'], 'string', 'max' => 5],
            [['visibility'], 'string', 'max' => 20],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::class,
                'targetAttribute' => ['description_pack_id' => 'description_pack_id'],
            ],
            [['code'], 'in', 'range' => fn() => $this->allowedTypes()],
            [['visibility'], 'in', 'range' => fn() => $this->allowedVisibilitiesForValidator()],
            [
                ['point_in_time_start_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => PointInTime::class,
                'targetAttribute' => ['point_in_time_start_id' => 'point_in_time_id'],
            ],
            [
                ['point_in_time_end_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => PointInTime::class,
                'targetAttribute' => ['point_in_time_end_id' => 'point_in_time_id'],
            ],
            [
                ['point_in_time_still_valid_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => PointInTime::class,
                'targetAttribute' => ['point_in_time_still_valid_id' => 'point_in_time_id'],
            ],
            [
                ['created_by'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['created_by' => 'id'],
            ],
            [
                ['updated_by'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['updated_by' => 'id'],
            ],
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!$insert) {
            $this->createHistoryRecord();
        }

        $this->public_text_expanded = $this->expandText($this->public_text);
        $this->protected_text_expanded = $this->expandText($this->protected_text);
        $this->private_text_expanded = $this->expandText($this->private_text);

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        if (!empty($changedAttributes)) {
            $this->descriptionPack->touch('updated_at');
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return array<string,string>
     */
    public function attributeHints(): array
    {
        return [
            'public_text' => Yii::t('app', 'DESCRIPTION_HINT_TEXT_PUBLIC'),
            'protected_text' => Yii::t('app', 'DESCRIPTION_HINT_TEXT_PROTECTED'),
            'private_text' => Yii::t('app', 'DESCRIPTION_HINT_TEXT_PRIVATE'),
        ];
    }

    /**
     * @return array<string,string>
     */
    public function attributeLabels(): array
    {
        return [
            'description_id' => Yii::t('app', 'DESCRIPTION_ID'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
            'title' => Yii::t('app', 'DESCRIPTION_TITLE'),
            'code' => Yii::t('app', 'DESCRIPTION_CODE'),
            'public_text' => Yii::t('app', 'DESCRIPTION_TEXT_PUBLIC'),
            'protected_text' => Yii::t('app', 'DESCRIPTION_TEXT_PROTECTED'),
            'private_text' => Yii::t('app', 'DESCRIPTION_TEXT_PRIVATE'),
            'public_text_expanded' => Yii::t('app', 'DESCRIPTION_TEXT_PUBLIC_EXPANDED'),
            'protected_text_expanded' => Yii::t('app', 'DESCRIPTION_TEXT_PROTECTED_EXPANDED'),
            'private_text_expanded' => Yii::t('app', 'DESCRIPTION_TEXT_PRIVATE_EXPANDED'),
            'lang' => Yii::t('app', 'LABEL_LANGUAGE'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'point_in_time_start_id' => Yii::t('app', 'DESCRIPTION_POINT_IN_TIME_START'),
            'point_in_time_end_id' => Yii::t('app', 'DESCRIPTION_POINT_IN_TIME_END'),
            'point_in_time_still_valid_id' => Yii::t('app', 'DESCRIPTION_POINT_IN_TIME_STILL_VALID'),
        ];
    }

    public function behaviors(): array
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::class,
                'positionAttribute' => 'position',
                'groupAttributes' => ['description_pack_id'],
            ],
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'description_id',
                'className' => 'Description',
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
     * @return array<string,string>
     */
    static public function typeNames(): array
    {
        return [
            self::TYPE_APPEARANCE => Yii::t('app', 'DESCRIPTION_TYPE_APPEARANCE'),
            self::TYPE_ASPECTS => Yii::t('app', 'DESCRIPTION_TYPE_ASPECTS'),
            self::TYPE_ATTITUDE => Yii::t('app', 'DESCRIPTION_TYPE_ATTITUDE'),
            self::TYPE_BACKGROUND => Yii::t('app', 'DESCRIPTION_TYPE_BACKGROUND'),
            self::TYPE_COMMENTARY => Yii::t('app', 'DESCRIPTION_TYPE_COMMENTARY'),
            self::TYPE_DOMAIN => Yii::t('app', 'DESCRIPTION_TYPE_DOMAIN'),
            self::TYPE_FAME => Yii::t('app', 'DESCRIPTION_TYPE_FAME'),
            self::TYPE_FACTIONS => Yii::t('app', 'DESCRIPTION_TYPE_FACTIONS'),
            self::TYPE_INTERACTIONS => Yii::t('app', 'DESCRIPTION_TYPE_INTERACTIONS'),
            self::TYPE_HISTORY => Yii::t('app', 'DESCRIPTION_TYPE_HISTORY'),
            self::TYPE_PERSONALITY => Yii::t('app', 'DESCRIPTION_TYPE_PERSONALITY'),
            self::TYPE_RESOURCES => Yii::t('app', 'DESCRIPTION_TYPE_RESOURCES'),
            self::TYPE_REPUTATION => Yii::t('app', 'DESCRIPTION_TYPE_REPUTATION'),
            self::TYPE_RETINUE => Yii::t('app', 'DESCRIPTION_TYPE_RETINUE'),
            self::TYPE_RUMOURS => Yii::t('app', 'DESCRIPTION_TYPE_RUMOURS'),
            self::TYPE_STORIES => Yii::t('app', 'DESCRIPTION_TYPE_STORIES'),
            self::TYPE_THREADS => Yii::t('app', 'DESCRIPTION_TYPE_THREADS'),
            self::TYPE_WHO => Yii::t('app', 'DESCRIPTION_TYPE_WHO'),
            self::TYPE_PREMISE => Yii::t('app', 'DESCRIPTION_TYPE_PREMISE'),
            self::TYPE_ACTORS => Yii::t('app', 'DESCRIPTION_TYPE_ACTORS'),
            self::TYPE_PLAN => Yii::t('app', 'DESCRIPTION_TYPE_PLAN'),
            self::TYPE_SCENE => Yii::t('app', 'DESCRIPTION_TYPE_SCENE'),
            self::TYPE_ACT => Yii::t('app', 'DESCRIPTION_TYPE_ACT'),
            self::TYPE_BRIEFING => Yii::t('app', 'DESCRIPTION_TYPE_BRIEFING'),
            self::TYPE_DEBRIEFING => Yii::t('app', 'DESCRIPTION_TYPE_DEBRIEFING'),
            self::TYPE_PRELUDE => Yii::t('app', 'DESCRIPTION_TYPE_PRELUDE'),
            self::TYPE_INTERLUDE => Yii::t('app', 'DESCRIPTION_TYPE_INTERLUDE'),
            self::TYPE_POSTLUDE => Yii::t('app', 'DESCRIPTION_TYPE_POSTLUDE'),
        ];
    }

    /**
     * Provides list of allowed description types for the class the object belongs to
     * List is provided as full names in current language keyed by codes
     *
     * Fallback mechanism: if the method is not implemented, a full list is provided, which will allow the object to be
     * used, but will reduce the user experience.
     *
     * @return string[]
     */
    public function typeNamesForThisClass(): array
    {
        $typeNamesAll = self::typeNames();
        $typeNamesAccepted = [];

        $class = 'common\models\\' . $this->descriptionPack->class;

        if (method_exists($class, 'allowedDescriptionTypes')) {
            $typesAllowed = call_user_func([$class, 'allowedDescriptionTypes']);
        } else {
            $typesAllowed = array_keys($typeNamesAll);
        }

        foreach ($typesAllowed as $typeKey) {
            if (isset($typeNamesAll[$typeKey])) {
                $typeNamesAccepted[$typeKey] = $typeNamesAll[$typeKey];
            }
        }

        return $typeNamesAccepted;
    }

    /**
     * @return array<int,string>
     */
    public function allowedTypes(): array
    {
        return array_keys(self::typeNames());
    }

    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getDescriptionPack(): ActiveQuery
    {
        return $this->hasOne(DescriptionPack::class, ['description_pack_id' => 'description_pack_id']);
    }

    public function getPointInTimeStart(): ActiveQuery
    {
        return $this->hasOne(PointInTime::class, ['point_in_time_id' => 'point_in_time_start_id']);
    }

    public function getPointInTimeEnd(): ActiveQuery
    {
        return $this->hasOne(PointInTime::class, ['point_in_time_id' => 'point_in_time_end_id']);
    }

    public function getPointInTimeStillValid(): ActiveQuery
    {
        return $this->hasOne(PointInTime::class, ['point_in_time_id' => 'point_in_time_still_valid_id']);
    }

    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getTypeName(): string
    {
        $names = self::typeNames();
        return isset($names[$this->code]) ? $names[$this->code] : "?";
    }

    public function getLanguage(): ?string
    {
        $language = Language::create($this->lang);
        return $language->getName();
    }

    public function getPublicFormatted(): string
    {
        return $this->formatText($this->public_text_expanded);
    }

    public function getProtectedFormatted(): string
    {
        return $this->formatText($this->protected_text_expanded);
    }

    public function getPrivateFormatted(): string
    {
        return $this->formatText($this->private_text_expanded);
    }

    /**
     * Provides simple representation of the object content, fit for basic display in an index or a summary
     * @return array<string,string>
     */
    public function getSimpleDataForApi(): array
    {
        return [
            'title' => $this->getTypeName(),
        ];
    }

    /**
     * Provides complete representation of public parts of object content, fit for full card display
     * @return array<string,string>
     */
    public function getCompleteDataForApi(): array
    {
        return [
            'title' => $this->getTypeName(),
            'text' => $this->getPublicFormatted(),
        ];
    }

    public function isVisibleInApi(): bool
    {
        return ($this->getVisibility() === Visibility::VISIBILITY_FULL);
    }

    public function createHistoryRecord(): ?DescriptionHistory
    {
        $description = Description::findOne(['description_id' => $this->description_id]);

        if (
            ($description->public_text === $this->public_text) &&
            ($description->protected_text === $this->protected_text) &&
            ($description->private_text === $this->private_text)) {
            return null;
        }

        return DescriptionHistory::createFromDescription($description);
    }

    /**
     * Provides word count that player can see
     */
    public function getWordCount(): int
    {
        return $this->getWordCountForPublic();
    }

    /**
     * Provides word count for public part
     */
    public function getWordCountForPublic(): int
    {
        return StringHelper::countWords($this->public_text_expanded);
    }

    /**
     * Provides word count for protected part
     */
    public function getWordCountForProtected(): int
    {
        return StringHelper::countWords($this->protected_text_expanded);
    }

    /**
     * Provides word count for private part
     */
    public function getWordCountForPrivate(): int
    {
        return StringHelper::countWords($this->private_text_expanded);
    }
}
