<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\FrontStyles;
use common\models\core\HasKey;
use common\models\core\HasParameters;
use common\models\core\HasSightings;
use common\models\core\HasStatus;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use Override;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception as DbException;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * This is the model class for table "epic".
 *
 * @property string $epic_id
 * @property string $key
 * @property string $name Public name for the epic
 * @property string $system Code for the system used
 * @property string|null $style Default style
 * @property string $status Epic status
 * @property int|null $current_story_id
 * @property string $parameter_pack_id
 * @property string $seen_pack_id
 * @property string $utility_bag_id
 *
 * @property CharacterSheet[] $characters
 * @property ParameterPack $parameterPack
 * @property SeenPack $seenPack
 * @property UtilityBag $utilityBag
 * @property User[] $gms
 * @property User[] $players
 * @property Article[] $articles
 * @property Game[] $games
 * @property Group[] $groups
 * @property Participant[] $participants
 * @property User[] $users
 * @property PointInTime[] $pointsInTime
 * @property Character[] $people
 * @property Recap[] $recaps
 * @property Scenario[] $scenarios
 * @property Story[] $stories
 * @property Story $currentStory
 *
 * @todo: Someday, system field will have to come from a closed list of supported systems
 */
class Epic extends ActiveRecord implements Displayable, HasParameters, HasSightings, HasStatus, HasKey
{
    use ToolsForEntity;

    const string STATUS_PROPOSED = 'proposed';       // idea is created; next: PREPARED, SCRAPPED
    const string STATUS_PLANNED = 'planning';        // epic is being planned; next: PREPARED, SCRAPPED
    const string STATUS_PREPARED = 'preparation';    // epic is being prepared; next: READY, SCRAPPED
    const string STATUS_READY = 'ready';             // epic is ready to run; next: PLAYED, SCRAPPED
    const string STATUS_SCRAPPED = 'scrapped';       // epic failed to achieve readiness; next: PLANNED, CLOSED
    const string STATUS_CANCELLED = 'cancelled';     // epic ran but failed to complete; next: RESUMING, CLOSED
    const string STATUS_PLAYED = 'played';           // in progress; next: LAPSED, ON HOLD, CANCELLED, FINISHED
    const string STATUS_LAPSED = 'lapsed';           // sessions stopped, but nothing was said yet; next: ON HOLD, CANCELLED, RESUMING
    const string STATUS_ON_HOLD = 'on hold';         // epic was officially suspended; next: RESUMING, CANCELLED
    const string STATUS_RESUMING = 'resuming';       // resuming after some trouble; next: PLAYED, ON HOLD
    const string STATUS_FINISHED = 'finished';       // epic was completed; next: CLOSED
    const string STATUS_CLOSED = 'closed';           // epic is documented and done; next: none

    #[Override]
    public static function tableName(): string
    {
        return 'epic';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'epic';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['name', 'system'], 'required'],
            [['name'], 'string', 'max' => 80],
            [['system', 'style'], 'string', 'max' => 20],
            [['status'], 'string', 'max' => 20],
            [['current_story_id'], 'integer'],
            [
                ['current_story_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Story::class,
                'targetAttribute' => ['current_story_id' => 'story_id'],
            ],
            [
                ['status'],
                'in',
                'range' => $this->getAllowedChange(),
                'message' => Yii::t(
                    'app',
                    'EPIC_STATUS_NOT_ALLOWED {allowed}',
                    ['allowed' => implode(', ', $this->getAllowedChangeNames())],
                )
            ],
            [
                ['parameter_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ParameterPack::class,
                'targetAttribute' => ['parameter_pack_id' => 'parameter_pack_id'],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    public function attributeLabels(): array
    {
        return [
            'epic_id' => Yii::t('app', 'EPIC_ID'),
            'key' => Yii::t('app', 'EPIC_KEY'),
            'name' => Yii::t('app', 'EPIC_NAME'),
            'system' => Yii::t('app', 'EPIC_GAME_SYSTEM'),
            'style' => Yii::t('app', 'EPIC_STYLE_FOR_FRONT'),
            'status' => Yii::t('app', 'EPIC_STATUS'),
            'current_story_id' => Yii::t('app', 'CURRENT_STORY'),
            'parameter_pack_id' => Yii::t('app', 'PARAMETER_PACK'),
            'seen_pack_id' => Yii::t('app', 'SEEN_PACK'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
        ];
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    static public function statusNames(): array
    {
        return [
            self::STATUS_CANCELLED => Yii::t('app', 'EPIC_STATUS_CANCELLED'),
            self::STATUS_CLOSED => Yii::t('app', 'EPIC_STATUS_CLOSED'),
            self::STATUS_FINISHED => Yii::t('app', 'EPIC_STATUS_FINISHED'),
            self::STATUS_LAPSED => Yii::t('app', 'EPIC_STATUS_LAPSED'),
            self::STATUS_ON_HOLD => Yii::t('app', 'EPIC_STATUS_ON_HOLD'),
            self::STATUS_PLANNED => Yii::t('app', 'EPIC_STATUS_PLANNED'),
            self::STATUS_PREPARED => Yii::t('app', 'EPIC_STATUS_PREPARED'),
            self::STATUS_PLAYED => Yii::t('app', 'EPIC_STATUS_PLAYED'),
            self::STATUS_PROPOSED => Yii::t('app', 'EPIC_STATUS_PROPOSED'),
            self::STATUS_READY => Yii::t('app', 'EPIC_STATUS_READY'),
            self::STATUS_RESUMING => Yii::t('app', 'EPIC_STATUS_RESUMING'),
            self::STATUS_SCRAPPED => Yii::t('app', 'EPIC_STATUS_SCRAPPED'),
        ];
    }


    /**
     * @return array<string, string>
     */
    #[Override]
    static public function statusClasses(): array
    {
        return [
            self::STATUS_CANCELLED => 'epic-status-cancelled',
            self::STATUS_CLOSED => 'epic-status-closed',
            self::STATUS_FINISHED => 'epic-status-finished',
            self::STATUS_LAPSED => 'epic-status-lapsed',
            self::STATUS_ON_HOLD => 'epic-status-on-hold',
            self::STATUS_PLANNED => 'epic-status-planned',
            self::STATUS_PREPARED => 'epic-status-prepared',
            self::STATUS_PLAYED => 'epic-status-played',
            self::STATUS_PROPOSED => 'epic-status-proposed',
            self::STATUS_READY => 'epic-status-ready',
            self::STATUS_RESUMING => 'epic-status-resuming',
            self::STATUS_SCRAPPED => 'epic-status-scrapped',
        ];
    }


    /**
     * @return array<string,string[]>
     */
    #[Override]
    public function statusAllowedChanges(): array
    {
        return [
            self::STATUS_CANCELLED => [self::STATUS_RESUMING, self::STATUS_CLOSED],
            self::STATUS_CLOSED => [],
            self::STATUS_FINISHED => [self::STATUS_CLOSED],
            self::STATUS_LAPSED => [self::STATUS_ON_HOLD, self::STATUS_CANCELLED, self::STATUS_RESUMING],
            self::STATUS_ON_HOLD => [self::STATUS_RESUMING, self::STATUS_CANCELLED],
            self::STATUS_PLANNED => [self::STATUS_PREPARED, self::STATUS_SCRAPPED],
            self::STATUS_PREPARED => [self::STATUS_READY, self::STATUS_SCRAPPED],
            self::STATUS_PLAYED => [
                self::STATUS_LAPSED,
                self::STATUS_ON_HOLD,
                self::STATUS_CANCELLED,
                self::STATUS_FINISHED,
            ],
            self::STATUS_PROPOSED => [self::STATUS_PLANNED, self::STATUS_SCRAPPED],
            self::STATUS_READY => [self::STATUS_PLAYED, self::STATUS_SCRAPPED],
            self::STATUS_RESUMING => [self::STATUS_PLAYED, self::STATUS_ON_HOLD],
            self::STATUS_SCRAPPED => [self::STATUS_PLANNED, self::STATUS_CLOSED],
        ];
    }

    /**
     * Provides sorting priorities based on status
     * Note: most important statuses have the lowest numbers
     *
     * @return int[]
     */
    public function sortPriorities(): array
    {
        return [
            self::STATUS_CANCELLED => 3,
            self::STATUS_CLOSED => 4,
            self::STATUS_FINISHED => 3,
            self::STATUS_LAPSED => 1,
            self::STATUS_ON_HOLD => 2,
            self::STATUS_PLANNED => 1,
            self::STATUS_PREPARED => 1,
            self::STATUS_PLAYED => 0,
            self::STATUS_PROPOSED => 2,
            self::STATUS_READY => 0,
            self::STATUS_RESUMING => 0,
            self::STATUS_SCRAPPED => 3,
        ];
    }

    /**
     * Provides Epic's own priority
     */
    public function getOwnSortPriority(): int
    {
        return $this->sortPriorities()[$this->status];
    }

    #[Override]
    public function getStatus(): string
    {
        $names = self::statusNames();
        return isset($names[$this->status]) ? $names[$this->status] : '?';
    }

    #[Override]
    public function getStatusClass(): string
    {
        $names = self::statusClasses();
        return isset($names[$this->status]) ? $names[$this->status] : '';
    }

    #[Override]
    public function getAllowedChange(): array
    {
        return array_merge(($this->statusAllowedChanges()[$this->status] ?? []), [$this->status]);
    }

    #[Override]
    public function getAllowedChangeNames(): array
    {
        return array_filter(self::statusNames(), function ($key) {
            return in_array($key, $this->getAllowedChange());
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getAllowedStoriesForDropDown(): array
    {
        $list = [];
        foreach ($this->getStories()->where([
            'visibility' => Visibility::VISIBILITY_FULL
        ])->orderBy('position DESC')->all() as $story) {
            /** @var Story $story */
            $list[$story->story_id] = $story->name;
        }
        return $list;
    }

    public function getStyle(): FrontStyles
    {
        return FrontStyles::tryFrom($this->style ?? FrontStyles::Default->value) ?? FrontStyles::Default;
    }

    /**
     * @throws DbException
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
     * @throws DbException
     */
    #[Override]
    public function afterSave($insert, $changedAttributes): void
    {
        $this->seenPack->updateRecord();
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @throws DbException
     * @throws HttpException
     */
    #[Override]
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
        }

        if (empty($this->parameter_pack_id)) {
            $pack = ParameterPack::create('Epic');
            $this->parameter_pack_id = $pack->parameter_pack_id;
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('Epic');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('Epic');
            $this->utility_bag_id = $pack->utility_bag_id;
        }

        return parent::beforeSave($insert);
    }

    #[Override]
    public function behaviors(): array
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'epic_id',
                'className' => 'Epic',
            ]
        ];
    }

    public function getArticles(): ActiveQuery
    {
        return $this->hasMany(Article::class, ['epic_id' => 'epic_id']);
    }

    public function getCharacters(): ActiveQuery
    {
        return $this->hasMany(CharacterSheet::class, ['epic_id' => 'epic_id']);
    }

    public function getCurrentStory(): ActiveQuery
    {
        return $this->hasOne(Story::class, ['story_id' => 'current_story_id']);
    }

    public function getParameterPack(): ActiveQuery
    {
        return $this->hasOne(ParameterPack::class, ['parameter_pack_id' => 'parameter_pack_id']);
    }

    public function getUtilityBag(): ActiveQuery
    {
        return $this->hasOne(UtilityBag::class, ['utility_bag_id' => 'utility_bag_id']);
    }

    public function getGames(): ActiveQuery
    {
        return $this->hasMany(Game::class, ['epic_id' => 'epic_id']);
    }

    public function getPointsInTime(): ActiveQuery
    {
        return $this->hasMany(PointInTime::class, ['epic_id' => 'epic_id']);
    }

    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(Group::class, ['epic_id' => 'epic_id']);
    }

    public function getGms(): ActiveQuery
    {
        return $this->getParticipants()->joinWith('participantRoles')->onCondition("role = 'gm'");
    }

    public function getPlayers(): ActiveQuery
    {
        return $this->getParticipants()->joinWith('participantRoles')->onCondition("role = 'player'");
    }

    public function getParticipants(): ActiveQuery
    {
        return $this->hasMany(Participant::class, ['epic_id' => 'epic_id']);
    }

    public function getPeople(): ActiveQuery
    {
        return $this->hasMany(Character::class, ['epic_id' => 'epic_id']);
    }

    public function getRecaps(): ActiveQuery
    {
        return $this->hasMany(Recap::class, ['epic_id' => 'epic_id']);
    }

    public function getSeenPack(): ActiveQuery
    {
        return $this->hasOne(SeenPack::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getStories(): ActiveQuery
    {
        return $this->hasMany(Story::class, ['epic_id' => 'epic_id']);
    }

    public function getCurrentRecap(): ?Recap
    {
        $query = new ActiveDataProvider(['query' => $this->getRecaps()->orderBy('time ASC')]);
        $recaps = $query->getModels();

        if ($recaps) {
            $recap = array_pop($recaps);
        } else {
            $recap = null;
        }

        return $recap;
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    public function getSimpleDataForApi(): array
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
        ];
    }

    #[Override]
    public function getCompleteDataForApi(): array
    {
        $query = new ActiveDataProvider(['query' => $this->getStories()->orderBy('story_id DESC')]);

        /* @var $stories Story[] */
        $stories = $query->getModels();
        $storyData = [];
        foreach ($stories as $story) {
            if ($story->isVisibleInApi()) {
                $storyData[] = $story->getSimpleDataForApi();
            }
        }

        return [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
            'current' => $this->getCurrentRecap()?->getCompleteDataForApi(),
            'stories' => $storyData,
        ];
    }

    #[Override]
    public function isVisibleInApi(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    #[Override]
    static public function allowedParameterTypes(): array
    {
        return [
            Parameter::SESSION_COUNT,
            Parameter::PCS_ACTIVE,
            Parameter::CS_ACTIVE,
            Parameter::DATA_SOURCE_FOR_REPUTATION,
            Parameter::EPIC_STATUS,
            Parameter::EPIC_SYSTEM_STATE,
            Parameter::LANGUAGE,
        ];
    }

    #[Override]
    static public function availableParameterTypes(): array
    {
        return [
            Parameter::SESSION_COUNT,
            Parameter::PCS_ACTIVE,
            Parameter::CS_ACTIVE,
            Parameter::DATA_SOURCE_FOR_REPUTATION,
            Parameter::EPIC_SYSTEM_STATE,
            Parameter::LANGUAGE,
        ];
    }

    /**
     * Determines whether the user can list epics
     *
     * @throws HttpException
     */
    static public function canUserIndexEpic(): bool
    {
        if (Yii::$app->user->can('indexEpic')) {
            return true;
        }

        throw new HttpException(403, Yii::t('app', 'NO_RIGHT_TO_LIST_EPIC'));
    }

    /**
     * Determines whether the user can create an epic
     *
     * @throws HttpException
     */
    static public function canUserCreateEpic(): bool
    {
        if (Yii::$app->user->can('openEpic')) {
            return true;
        }

        throw new HttpException(403, Yii::t('app', 'NO_RIGHT_TO_CREATE_EPIC'));
    }

    /**
     * Determines whether the user can make changes to this epic
     *
     * @throws HttpException
     */
    public function canUserControlYou(): bool
    {
        if (Yii::$app->user->can('controlEpic', ['epic' => $this])) {
            return true;
        }

        throw new HttpException(403, Yii::t('app', 'NO_RIGHT_TO_CONTROL_EPIC'));
    }

    /**
     * Determines whether the user can view this epic
     *
     * @throws HttpException
     */
    public function canUserViewYou(): bool
    {
        if (Yii::$app->user->can('viewEpic', ['epic' => $this])) {
            return true;
        }

        throw new HttpException(403, Yii::t('app', 'NO_RIGHT_TO_VIEW_EPIC'));
    }

    /**
     * Determines whether the user can view active epic
     *
     * @throws HttpException
     */
    static public function canUserViewActiveEpic(): bool
    {
        if (isset(Yii::$app->params['activeEpic'])) {
            /** @var Epic $activeEpic */
            $activeEpic = Yii::$app->params['activeEpic'];
            return $activeEpic->canUserViewYou();
        }

        return false;
    }

    public function isUserYourParticipant(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return Participant::participantExists($user, $this);
    }

    public function isUserYourManager(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return Participant::participantHasRole($user, $this, ParticipantRole::ROLE_MANAGER);
    }

    public function attachCurrentUserAsManager(): bool
    {
        try {
            /** @var User $user */
            $user = Yii::$app->user->identity;

            if ($this->isUserYourManager($user)) {
                /* If they are the manager, there is nothing to add */
                return false;
            }

            $participant = Participant::findOne(['epic_id' => $this->epic_id, 'user_id' => $user->id]);

            if (!$participant) {
                $participant = new Participant(['epic_id' => $this->epic_id, 'user_id' => $user->id]);
                $participant->save();
                $participant->refresh();
            }

            $roles = $participant->getRoles();
            $roles[ParticipantRole::ROLE_MANAGER] = ParticipantRole::ROLE_MANAGER;
            $participant->roleChoices = $roles;
            $participant->setRoles();

            PerformedAction::createRecord(PerformedAction::PERFORMED_ACTION_MANAGER_ATTACH, 'Epic', $this->epic_id);
        } catch (Exception) {
            /* @todo Add logging */
            return false;
        }

        return true;
    }

    /**
     * @throws Throwable
     */
    public function detachCurrentUserAsManager(): bool
    {
        try {
            /** @var User $user */
            $user = Yii::$app->user->identity;

            if (!$this->isUserYourManager($user)) {
                /* If they are not the manager, there is nothing to remove */
                return false;
            }

            $participant = Participant::findOne(['epic_id' => $this->epic_id, 'user_id' => $user->id]);

            $roles = $participant->getRoles();
            unset($roles[ParticipantRole::ROLE_MANAGER]);
            $participant->roleChoices = $roles;
            $participant->setRoles();

            /* No roles - no need to keep the participant object */
            if (!$roles) {
                $participant->delete();
            }

            PerformedAction::createRecord(PerformedAction::PERFORMED_ACTION_MANAGER_DETACH, 'Epic', $this->epic_id);
        } catch (Exception) {
            /** @todo Add logging */
            return false;
        }

        return true;
    }

    /**
     * Provides list of players for a drop down
     *
     * @return string[]
     */
    public function getPlayerListForDropDown(): array
    {
        $list = [];
        foreach ($this->getPlayers()->all() as $player) {
            /** @var Participant $player */
            $list[$player->user_id] = $player->user->username;
        }
        return $list;
    }

    /**
     * @throws DbException
     */
    #[Override]
    public function recordSighting(): bool
    {
        return $this->seenPack->recordSighting();
    }

    /**
     * @throws DbException
     */
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

    /**
     * @throws HttpException
     */
    static function throwExceptionAboutCreate(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_EPIC'));
    }

    /**
     * @throws HttpException
     */
    static function throwExceptionAboutControl(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_EPIC'));
    }

    /**
     * @throws HttpException
     */
    static function throwExceptionAboutIndex(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_EPIC'));
    }

    /**
     * @throws HttpException
     */
    static function throwExceptionAboutView(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_EPIC'));
    }

    public function __toString()
    {
        return Html::a($this->name, ['epic/view', 'key' => $this->key]);
    }

    /**
     * Provides this object's ActiveQuery
     * NOTE: this is a workaround for `Parameter` class to work on Epic without giving it HasEpic control
     */
    public function getEpic(): ActiveQuery
    {
        return Epic::find()->where(['epic_id' => $this->epic_id]);
    }

    public function getGameCountByStatus(string $status): int
    {
        return count(array_filter($this->games, function (Game $game) use ($status) {
            return $game->status == $status;
        }));
    }

    /**
     * Sorts Epics by status and ID
     *
     * @param Epic[] $epics
     * @return Epic[]
     */
    static public function sortByStatus(array $epics): array
    {
        uasort($epics, function (Epic $a, Epic $b) {
            if ($a->getOwnSortPriority() === $b->getOwnSortPriority()) {
                return $a->epic_id > $b->epic_id ? -1 : 1; // it impossible to have the same ID and with this, the sorting is deterministic
            }
            return $a->getOwnSortPriority() < $b->getOwnSortPriority() ? -1 : 1;
        });

        return $epics;
    }
}
