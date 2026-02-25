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
use common\models\tools\ToolsForLinkTags;
use common\models\tools\ToolsForMultipleChoiceFields;
use common\models\type\StoryType;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\helpers\StringHelper;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "story".
 *
 * @property int $story_id
 * @property int $epic_id
 * @property string $key
 * @property string $name
 * @property string $short
 * @property string|null $long
 * @property string|null $short_expanded
 * @property string|null $long_expanded
 * @property int|null $position
 * @property string $visibility
 * @property int|null $based_on_id
 * @property string $code
 * @property string|null $data
 * @property int|null $parameter_pack_id
 * @property int|null $seen_pack_id
 * @property int|null $utility_bag_id
 *
 * @property Epic $epic
 * @property Scenario|null $basedOn
 * @property ParameterPack $parameterPack
 * @property SeenPack $seenPack
 * @property StoryCharacterAssignment[] $storyCharacterAssignments
 * @property StoryGroupAssignment[] $storyGroupAssignments
 * @property Parameter[] $storyParameters
 * @property UtilityBag $utilityBag
 */
class Story extends ActiveRecord implements Displayable, HasParameters, HasEpicControl, HasSightings, HasVisibility
{
    use ToolsForEntity;
    use ToolsForHasVisibility;
    use ToolsForMultipleChoiceFields;
    use ToolsForLinkTags;

    public array|string $storyCharacterAssignmentChoicesPublic = [];
    public array|string $storyCharacterAssignmentChoicesPrivate = [];
    public array|string $storyGroupAssignmentChoicesPublic = [];
    public array|string $storyGroupAssignmentChoicesPrivate = [];

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
            [['code', 'visibility'], 'string', 'max' => 20],
            [['is_off_the_record_change'], 'boolean'],
            [['storyCharacterAssignmentChoicesPublic', 'storyCharacterAssignmentChoicesPrivate'], 'safe'],
            [['storyGroupAssignmentChoicesPublic', 'storyGroupAssignmentChoicesPrivate'], 'safe'],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['based_on_id'],
                'exist',
                'skipOnError' => false,
                'targetClass' => Scenario::class,
                'targetAttribute' => ['based_on_id' => 'scenario_id']
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
            [
                ['code'],
                'in',
                'range' => fn() => StoryType::allowedCodes(),
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
            'short_expanded' => Yii::t('app', 'STORY_SHORT'),
            'long_expanded' => Yii::t('app', 'STORY_LONG'),
            'position' => Yii::t('app', 'STORY_POSITION'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'code' => Yii::t('app', 'STORY_TYPE'),
            'based_on_id' => Yii::t('app', 'STORY_SCENARIO'),
            'data' => Yii::t('app', 'STORY_DATA'),
            'parameter_pack_id' => Yii::t('app', 'PARAMETER_PACK'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
            'is_off_the_record_change' => Yii::t('app', 'CHECK_OFF_THE_RECORD_CHANGE'),
            'storyCharacterAssignmentChoicesPublic' => Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PUBLIC'),
            'storyCharacterAssignmentChoicesPrivate' => Yii::t('app', 'STORY_ASSIGNMENT_CHARACTERS_PRIVATE'),
            'storyGroupAssignmentChoicesPublic' => Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PUBLIC'),
            'storyGroupAssignmentChoicesPrivate' => Yii::t('app', 'STORY_ASSIGNMENT_GROUPS_PRIVATE'),
        ];
    }

    public function afterFind(): void
    {
        if ($this->seen_pack_id) {
            $this->seenPack->recordNotification();
        }

        $this->storyCharacterAssignmentChoicesPublic = $this->getStoryCharacterAssignmentIds(Visibility::VISIBILITY_FULL);
        $this->storyCharacterAssignmentChoicesPrivate = $this->getStoryCharacterAssignmentIds(Visibility::VISIBILITY_GM);

        $this->storyGroupAssignmentChoicesPublic = $this->getStoryGroupAssignmentIds(Visibility::VISIBILITY_FULL);
        $this->storyGroupAssignmentChoicesPrivate = $this->getStoryGroupAssignmentIds(Visibility::VISIBILITY_GM);

        parent::afterFind();
    }

    /**
     * @throws Exception
     */
    public function afterSave($insert, $changedAttributes): void
    {
        if (!$this->is_off_the_record_change) {
            $this->seenPack->updateRecord();
        }

        $this->setStoryCharacterAssignmentIds(
            $this->normalizeInputFromMultiSelect($this->storyCharacterAssignmentChoicesPublic),
            $this->normalizeInputFromMultiSelect($this->storyCharacterAssignmentChoicesPrivate)
        );

        $this->setStoryGroupAssignmentIds(
            $this->normalizeInputFromMultiSelect($this->storyGroupAssignmentChoicesPublic),
            $this->normalizeInputFromMultiSelect($this->storyGroupAssignmentChoicesPrivate)
        );

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

        $this->short_expanded = $this->expandText($this->short);
        $this->long_expanded = $this->expandText($this->long);

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

    public function getStoryCharacterAssignments(): ActiveQuery
    {
        return $this->hasMany(StoryCharacterAssignment::class, ['story_id' => 'story_id']);
    }

    public function getStoryCharacterAssignmentsByVisibility(Visibility $visibility): ActiveQuery
    {
        return $this->hasMany(StoryCharacterAssignment::class, ['story_id' => 'story_id'])
            ->andWhere(['story_character_assignment.visibility' => $visibility->value]);
    }

    public function getStoryCharacterAssignmentIds(Visibility $visibility): array
    {
        return $this->getStoryCharacterAssignmentsByVisibility($visibility)->select('character_id')->column();
    }

    public function getStoryCharacterAssignmentLinks(Visibility $visibility): array
    {
        $assignments = $this
            ->getStoryCharacterAssignmentsByVisibility($visibility)->joinWith('character')
            ->orderBy('character.name ASC')
            ->all();

        return array_map(
            fn(StoryCharacterAssignment $assignment) => Html::a(
                $assignment->character->name,
                ['character/view', 'key' => $assignment->character->key]
            ),
            $assignments
        );
    }

    public function getStoryGroupAssignments(): ActiveQuery
    {
        return $this->hasMany(StoryGroupAssignment::class, ['story_id' => 'story_id']);
    }

    public function getStoryGroupAssignmentsByVisibility(Visibility $visibility): ActiveQuery
    {
        return $this->hasMany(StoryGroupAssignment::class, ['story_id' => 'story_id'])
            ->andWhere(['story_group_assignment.visibility' => $visibility->value]);
    }

    public function getStoryGroupAssignmentIds(Visibility $visibility): array
    {
        return $this->getStoryGroupAssignmentsByVisibility($visibility)->select('group_id')->column();
    }

    public function getStoryGroupAssignmentLinks(Visibility $visibility): array
    {
        $assignments = $this
            ->getStoryGroupAssignmentsByVisibility($visibility)->joinWith('group')
            ->orderBy('group.name ASC')
            ->all();

        return array_map(
            fn(StoryGroupAssignment $assignment) => Html::a(
                $assignment->group->name,
                ['group/view', 'key' => $assignment->group->key]
            ),
            $assignments
        );
    }

    public function getStoryParameters(): ActiveQuery
    {
        return $this->hasMany(Parameter::class, ['story_id' => 'story_id']);
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
        return Markdown::process($this->short_expanded ?? $this->short, 'gfm');
    }

    /**
     * Provides story summary formatted in HTML
     */
    public function getLongFormatted(): string
    {
        return Markdown::process($this->long_expanded ?? $this->long, 'gfm');
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

    public function getCodeName(): ?string
    {
        return StoryType::tryFrom($this->code)?->name();
    }

    public function hasCodeName(): bool
    {
        return !empty($this->code);
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

    /**
     * @throws Exception
     */
    public function setStoryCharacterAssignmentIds(array $idsPublic, array $idsPrivate): void
    {
        // todo consider transactions
        StoryCharacterAssignment::deleteAll(['story_id' => $this->story_id]);

        foreach ($this->normalizeIntegerInput($idsPublic) as $id) {
            $assignment = new StoryCharacterAssignment([
                'story_id' => $this->story_id,
                'character_id' => $id,
                'visibility' => Visibility::VISIBILITY_FULL->value,
            ]);
            $assignment->save();
        }

        foreach ($this->normalizeIntegerInput($idsPrivate) as $id) {
            $assignment = new StoryCharacterAssignment([
                'story_id' => $this->story_id,
                'character_id' => $id,
                'visibility' => Visibility::VISIBILITY_GM->value,
            ]);
            $assignment->save();
        }
    }

    /**
     * @throws Exception
     */
    public function setStoryGroupAssignmentIds(array $idsPublic, array $idsPrivate): void
    {
        // todo consider transactions
        StoryGroupAssignment::deleteAll(['story_id' => $this->story_id]);

        foreach ($this->normalizeIntegerInput($idsPublic) as $id) {
            $assignment = new StoryGroupAssignment([
                'story_id' => $this->story_id,
                'group_id' => $id,
                'visibility' => Visibility::VISIBILITY_FULL->value,
            ]);
            $assignment->save();
        }

        foreach ($this->normalizeIntegerInput($idsPrivate) as $id) {
            $assignment = new StoryGroupAssignment([
                'story_id' => $this->story_id,
                'group_id' => $id,
                'visibility' => Visibility::VISIBILITY_GM->value,
            ]);
            $assignment->save();
        }
    }

    public function getBasedOn(): ActiveQuery
    {
        return $this->hasOne(Scenario::class, ['scenario_id' => 'based_on_id']);
    }


    public function __toString()
    {
        return Html::a($this->name, ['story/view', 'key' => $this->key]);
    }
}
