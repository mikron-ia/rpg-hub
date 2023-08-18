<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasParameters;
use common\models\core\HasSightings;
use common\models\core\HasStatus;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * This is the model class for table "epic".
 *
 * @property string $epic_id
 * @property string $key
 * @property string $name Public name for the epic
 * @property string $system Code for the system used
 * @property string $status Epic status
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
 *
 * @todo: Someday, system field will have to come from a closed list of supported systems
 */
class Epic extends ActiveRecord implements Displayable, HasParameters, HasSightings, HasStatus
{
    use ToolsForEntity;

    const STATUS_PROPOSED = 'proposed';       // idea is created; next: PREPARED, SCRAPPED
    const STATUS_PLANNED = 'planning';        // epic is being planned; next: PREPARED, SCRAPPED
    const STATUS_PREPARED = 'preparation';    // epic is being prepared; next: READY, SCRAPPED
    const STATUS_READY = 'ready';             // epic is ready to run; next: PLAYED, SCRAPPED
    const STATUS_SCRAPPED = 'scrapped';       // epic failed to achieve readiness; next: PLANNED, CLOSED
    const STATUS_CANCELLED = 'cancelled';     // epic ran, but failed to complete; next: RESUMING, CLOSED
    const STATUS_PLAYED = 'played';           // in progress; next: LAPSED, ON HOLD, CANCELLED, FINISHED
    const STATUS_LAPSED = 'lapsed';           // sessions stopped, but nothing was said yet; next: ON HOLD, CANCELLED, RESUMING
    const STATUS_ON_HOLD = 'on hold';         // epic was officially suspended; next: RESUMING, CANCELLED
    const STATUS_RESUMING = 'resuming';       // resuming after some trouble; next: PLAYED, ON HOLD
    const STATUS_FINISHED = 'finished';       // epic was completed; next: CLOSED
    const STATUS_CLOSED = 'closed';           // epic is documented and done; next: none

    public static function tableName()
    {
        return 'epic';
    }

    public function rules()
    {
        return [
            [['name', 'system'], 'required'],
            [['name'], 'string', 'max' => 80],
            [['system'], 'string', 'max' => 20],
            [['status'], 'string', 'max' => 20],
            [
                ['status'],
                'in',
                'range' => $this->getAllowedChange(),
                'message' => Yii::t(
                    'app',
                    'EPIC_STATUS_NOT_ALLOWED {allowed}',
                    ['allowed' => implode(', ', $this->getAllowedChangeNames())]
                )
            ],
            [
                ['parameter_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ParameterPack::class,
                'targetAttribute' => ['parameter_pack_id' => 'parameter_pack_id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'epic_id' => Yii::t('app', 'EPIC_ID'),
            'key' => Yii::t('app', 'EPIC_KEY'),
            'name' => Yii::t('app', 'EPIC_NAME'),
            'system' => Yii::t('app', 'EPIC_GAME_SYSTEM'),
            'status' => Yii::t('app', 'EPIC_STATUS'),
            'parameter_pack_id' => Yii::t('app', 'PARAMETER_PACK'),
            'seen_pack_id' => Yii::t('app', 'SEEN_PACK'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
        ];
    }

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
                self::STATUS_FINISHED
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
     * @return int
     */
    public function getOwnSortPriority(): int
    {
        return $this->sortPriorities()[$this->status];
    }

    public function getStatus(): string
    {
        $names = self::statusNames();
        return isset($names[$this->status]) ? $names[$this->status] : '?';
    }

    public function getStatusClass(): string
    {
        $names = self::statusClasses();
        return isset($names[$this->status]) ? $names[$this->status] : '';
    }

    public function getAllowedChange(): array
    {
        return array_merge(($this->statusAllowedChanges()[$this->status] ?? []), [$this->status]);
    }

    public function getAllowedChangeNames(): array
    {
        return array_filter(self::statusNames(), function ($key) {
            return in_array($key, $this->getAllowedChange());
        }, ARRAY_FILTER_USE_KEY);
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
        $this->utilityBag->flagAsChanged();
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
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

    public function behaviors()
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

    /**
     * @return ActiveQuery
     */
    public function getCharacters()
    {
        return $this->hasMany(CharacterSheet::class, ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getParameterPack()
    {
        return $this->hasOne(ParameterPack::class, ['parameter_pack_id' => 'parameter_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUtilityBag()
    {
        return $this->hasOne(UtilityBag::class, ['utility_bag_id' => 'utility_bag_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGames()
    {
        return $this->hasMany(Game::class, ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPointsInTime()
    {
        return $this->hasMany(PointInTime::class, ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Group::class, ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGms()
    {
        return $this->getParticipants()->joinWith('participantRoles')->onCondition("role = 'gm'");
    }

    /**
     * @return ActiveQuery
     */
    public function getPlayers()
    {
        return $this->getParticipants()->joinWith('participantRoles')->onCondition("role = 'player'");
    }

    /**
     * @return ActiveQuery
     */
    public function getParticipants()
    {
        return $this->hasMany(Participant::class, ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasMany(Character::class, ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRecaps()
    {
        return $this->hasMany(Recap::class, ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSeenPack(): ActiveQuery
    {
        return $this->hasOne(SeenPack::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::class, ['epic_id' => 'epic_id']);
    }

    /**
     * @return Recap|null
     */
    public function getCurrentRecap()
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

    public function getSimpleDataForApi()
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
        ];
    }

    public function getCompleteDataForApi()
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

        $recap = $this->getCurrentRecap();
        $recapData = ($recap ? $recap->getCompleteDataForApi() : null);

        return [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
            'current' => $recapData,
            'stories' => $storyData,
        ];
    }

    public function isVisibleInApi()
    {
        return true;
    }

    /**
     * Provides list of types allowed by this class
     * @return string[]
     */
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

    /**
     * Determines whether user can list epics
     * @return bool
     * @throws HttpException
     */
    static public function canUserIndexEpic(): bool
    {
        if (Yii::$app->user->can('indexEpic')) {
            return true;
        } else {
            throw new HttpException(403, Yii::t('app', 'NO_RIGHT_TO_LIST_EPIC'));
        }
    }

    /**
     * Determines whether user can create an epic
     * @return bool
     * @throws HttpException
     */
    static public function canUserCreateEpic(): bool
    {
        if (Yii::$app->user->can('openEpic')) {
            return true;
        } else {
            throw new HttpException(403, Yii::t('app', 'NO_RIGHT_TO_CREATE_EPIC'));
        }
    }

    /**
     * Determines whether user can make changes to this epic
     * @return bool
     * @throws HttpException
     */
    public function canUserControlYou(): bool
    {
        if (Yii::$app->user->can('controlEpic', ['epic' => $this])) {
            return true;
        } else {
            throw new HttpException(403, Yii::t('app', 'NO_RIGHT_TO_CONTROL_EPIC'));
        }
    }

    /**
     * Determines whether user can view this epic
     * @return bool
     * @throws HttpException
     */
    public function canUserViewYou(): bool
    {
        if (Yii::$app->user->can('viewEpic', ['epic' => $this])) {
            return true;
        } else {
            throw new HttpException(403, Yii::t('app', 'NO_RIGHT_TO_VIEW_EPIC'));
        }
    }

    /**
     * Determines whether user can view active epic
     * @return bool
     * @throws HttpException
     */
    static public function canUserViewActiveEpic(): bool
    {
        if (isset(Yii::$app->params['activeEpic'])) {
            /** @var Epic $activeEpic */
            $activeEpic = Yii::$app->params['activeEpic'];
            return $activeEpic->canUserViewYou();
        } else {
            return false;
        }
    }

    /**
     * @param User|null $user
     * @return bool
     */
    public function isUserYourParticipant($user): bool
    {
        if (!$user) {
            return false;
        }

        return Participant::participantExists($user, $this);
    }

    /**
     * @param User|null $user
     * @return bool
     */
    public function isUserYourManager($user): bool
    {
        if (!$user) {
            return false;
        }

        return Participant::participantHasRole($user, $this, ParticipantRole::ROLE_MANAGER);
    }

    public function attachCurrentUserAsManager()
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
        } catch (Exception $e) {
            /* @todo Add logging */
            return false;
        }

        return true;
    }

    public function detachCurrentUserAsManager()
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
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Provides list of players for a drop down
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

    static function throwExceptionAboutCreate()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_EPIC'));
    }

    static function throwExceptionAboutControl()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_EPIC'));
    }

    static function throwExceptionAboutIndex()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_EPIC'));
    }

    static function throwExceptionAboutView()
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
     *
     * @return ActiveQuery
     */
    public function getEpic()
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
    static public function sortByStatus(array $epics)
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
