<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\core\HasKey;
use common\models\core\HasParameters;
use common\models\core\HasSightings;
use common\models\core\HasVisibility;
use common\models\core\Visibility;
use common\models\state\ProjectStatus;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasVisibility;
use common\models\tools\ToolsForLinkTags;
use common\models\type\ProjectType;
use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\web\HttpException;
use yii2tech\ar\position\PositionBehavior;

/**
 * @property int $project_id
 * @property int $epic_id
 * @property string $key
 * @property string $name
 * @property string $short
 * @property string|null $long
 * @property string|null $notes
 * @property string|null $short_expanded
 * @property string|null $long_expanded
 * @property string|null $notes_expanded
 * @property int|null $position
 * @property string $visibility
 * @property int|null $based_on_id
 * @property string $code
 * @property string $status
 * @property string|null $data
 * @property int|null $parameter_pack_id
 * @property int|null $seen_pack_id
 *
 * @property Epic $epic
 * @property Scenario|null $basedOn
 * @property ParameterPack $parameterPack
 * @property Parameter[] $projectParameters
 * @property SeenPack $seenPack
 *
 * @method moveNext()
 * @method movePrev()
 */
class Project extends ActiveRecord implements HasKey, HasParameters, HasEpicControl, HasSightings, HasVisibility
{
    use ToolsForEntity;
    use ToolsForHasVisibility;
    use ToolsForLinkTags;

    public bool $is_off_the_record_change = false;

    /**
     * @var array<string,string>
     */
    private array $parametersFormatted = [];

    #[Override]
    public static function tableName(): string
    {
        return 'project';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'project';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['epic_id', 'name', 'short'], 'required'],
            [['epic_id', 'position'], 'integer'],
            [['short', 'long', 'notes'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name'], 'string', 'max' => 120],
            [['code', 'visibility'], 'string', 'max' => 20],
            [['is_off_the_record_change'], 'boolean'],
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
                'targetAttribute' => ['based_on_id' => 'scenario_id'],
            ],
            [
                ['visibility'],
                'in',
                'range' => fn() => $this->allowedVisibilitiesForValidator(),
            ],
            [
                ['code'],
                'in',
                'range' => fn() => ProjectType::allowedCodes(),
            ],
            [
                ['status'],
                'in',
                'range' => fn() => ProjectStatus::listLegalValuesAsKeys(),
            ],
            [
                ['data'],
                function ($attribute, $params, $validator) {
                    if (!json_validate($this->$attribute)) {
                        $this->addError(
                            $attribute,
                            Yii::t('app', 'ERROR_DATA_JSON_INVALID {message}', ['message' => json_last_error_msg()])
                        );
                    }
                },
            ],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'project_id' => Yii::t('app', 'PROJECT_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'PROJECT_KEY'),
            'name' => Yii::t('app', 'PROJECT_NAME'),
            'short' => Yii::t('app', 'PROJECT_SHORT'),
            'long' => Yii::t('app', 'PROJECT_LONG'),
            'notes' => Yii::t('app', 'PROJECT_NOTES'),
            'short_expanded' => Yii::t('app', 'PROJECT_SHORT'),
            'long_expanded' => Yii::t('app', 'PROJECT_LONG'),
            'notes_expanded' => Yii::t('app', 'PROJECT_NOTES'),
            'position' => Yii::t('app', 'PROJECT_POSITION'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'code' => Yii::t('app', 'PROJECT_TYPE'),
            'status' => Yii::t('app', 'PROJECT_STATUS'),
            'based_on_id' => Yii::t('app', 'PROJECT_SCENARIO'),
            'data' => Yii::t('app', 'PROJECT_DATA'),
            'is_off_the_record_change' => Yii::t('app', 'CHECK_OFF_THE_RECORD_CHANGE'),
        ];
    }

    /**
     * @return array<string,string>
     */
    #[Override]
    public function attributeHints(): array
    {
        return [
            'short' => Yii::t('app', 'PROJECT_SHORT_HINT'),
            'long' => Yii::t('app', 'PROJECT_LONG_HINT'),
            'notes' => Yii::t('app', 'PROJECT_NOTES_HINT'),
        ];
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function afterFind(): void
    {
        if ($this->seen_pack_id) {
            $this->seenPack->recordNotification();
        }

        parent::afterFind();
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function afterSave($insert, $changedAttributes): void
    {
        if (!$this->is_off_the_record_change) {
            $this->seenPack->updateRecord();
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    #[Override]
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
            $this->data = json_encode([]);
        }

        if (empty($this->parameter_pack_id)) {
            $pack = ParameterPack::create('Project');
            $this->parameter_pack_id = $pack->parameter_pack_id;
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('Project');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        $this->short_expanded = $this->expandText($this->short);
        $this->long_expanded = $this->expandText($this->long);

        return parent::beforeSave($insert);
    }

    #[Override]
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
                'idName' => 'project_id',
                'className' => 'Project',
            ],
        ];
    }

    #[Override]
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

    public function getProjectParameters(): ActiveQuery
    {
        return $this->hasMany(Parameter::class, ['project_id' => 'project_id']);
    }

    public function getShortFormatted(): string
    {
        return $this->formatText($this->short_expanded ?? $this->short, false);
    }

    public function getLongFormatted(): string
    {
        return $this->formatText($this->long_expanded ?? $this->long, true);
    }

    public function getLongFormattedForOperator(): string
    {
        return $this->processSecretTagsForOperator($this->getLongFormatted());
    }

    public function getLongFormattedForUser(): string
    {
        return $this->processSecretTagsForUser($this->getLongFormatted());
    }

    public function getNotesFormatted(): string
    {
        return $this->formatText($this->notes_expanded ?? $this->notes, true);
    }

    public function getStatus(): ProjectStatus
    {
        return ProjectStatus::tryFrom($this->status) ?? ProjectStatus::Unknown;
    }

    public function getType(): ProjectType
    {
        return ProjectType::tryFrom($this->code) ?? ProjectType::None;
    }

    public function formatParameters(): array
    {
        if (!$this->parametersFormatted) {
            $parameters = [];

            foreach ($this->parameterPack->parameters as $parameter) {
                if ($parameter->getVisibility() === Visibility::Full) {
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
        return ProjectType::tryFrom($this->code)?->name();
    }

    public function displayCodeName(): bool
    {
        return ProjectType::tryFrom($this->code)?->displayTag() ?? false;
    }

    /**
     * @return string[]
     */
    #[Override]
    public static function allowedParameterTypes(): array
    {
        return [
            Parameter::TIME_RANGE,
            Parameter::LOCATION_POINT_START,
            Parameter::LOCATION_POINT_END,
            Parameter::PCS_ACTIVE,
            Parameter::CS_ACTIVE,
        ];
    }

    /**
     * @return string[]
     */
    #[Override]
    public static function availableParameterTypes(): array
    {
        return [
            Parameter::TIME_RANGE,
            Parameter::LOCATION_POINT_START,
            Parameter::LOCATION_POINT_END,
            Parameter::PCS_ACTIVE,
            Parameter::CS_ACTIVE,
        ];
    }

    #[Override]
    public static function canUserIndexThem(): bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    #[Override]
    public static function canUserCreateThem(): bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    #[Override]
    public function canUserControlYou(): bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    #[Override]
    public function canUserViewYou(): bool
    {
        return self::canUserViewInEpic($this->epic);
    }

    #[Override]
    static function throwExceptionAboutCreate(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_PROJECT'));
    }

    #[Override]
    static function throwExceptionAboutControl(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_PROJECT'));
    }

    #[Override]
    static function throwExceptionAboutIndex(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_PROJECT'));
    }

    #[Override]
    static function throwExceptionAboutView(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_PROJECT'));
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

    #[Override]
    public function recordSighting(): bool
    {
        return $this->seenPack->recordSighting();
    }

    #[Override]
    public function recordNotification(): bool
    {
        return $this->seenPack->recordNotification();
    }

    #[Override]
    public function showSightingStatus(): string
    {
        return $this->seenPack->getStatusForCurrentUser();
    }

    #[Override]
    public function showSightingCSS(): string
    {
        return $this->seenPack->getCSSForCurrentUser();
    }

    public function getLongDescriptionWordCount(): int
    {
        return StringHelper::countWords($this->long);
    }

    public function getBasedOn(): ActiveQuery
    {
        return $this->hasOne(Scenario::class, ['scenario_id' => 'based_on_id']);
    }

    public function __toString()
    {
        return Html::a($this->name, ['project/view', 'key' => $this->key]);
    }
}
