<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\Displayable;
use common\models\core\FrontStyle;
use common\models\core\HasKey;
use common\models\core\HasParameters;
use common\models\core\HasSightings;
use common\models\core\Visibility;
use common\models\state\EpicStatus;
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
 * @property Character[] $characters
 * @property CharacterSheet[] $characterSheets
 * @property ParameterPack $parameterPack
 * @property SeenPack $seenPack
 * @property UtilityBag $utilityBag
 * @property User[] $gms
 * @property User[] $players
 * @property Article[] $articles
 * @property Game[] $games
 * @property Group[] $groups
 * @property Location[] $locations
 * @property Participant[] $participants
 * @property PointInTime[] $pointsInTime
 * @property Project[] $projects
 * @property Recap[] $recaps
 * @property Scenario[] $scenarios
 * @property Story[] $stories
 * @property Story $currentStory
 *
 * @todo: Someday, system field will have to come from a closed list of supported systems
 */
class Epic extends ActiveRecord implements Displayable, HasParameters, HasSightings, HasKey
{
    use ToolsForEntity;

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
                'range' => $this->getStatus()->getAllowedSuccessorsAsKeys(),
                'message' => Yii::t(
                    'app',
                    'EPIC_STATUS_NOT_ALLOWED {allowed}',
                    ['allowed' => implode(', ', $this->getStatus()->getAllowedSuccessorsAsStrings())],
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

    public function getStatus(): EpicStatus
    {
        return $this->status === null ? EpicStatus::Proposed : EpicStatus::from($this->status);
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

    public function getStyle(): FrontStyle
    {
        return FrontStyle::tryFrom($this->style ?? FrontStyle::Default->value) ?? FrontStyle::Default;
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
        return $this->hasMany(Character::class, ['epic_id' => 'epic_id']);
    }

    public function getCharacterSheets(): ActiveQuery
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

    public function getLocations(): ActiveQuery
    {
        return $this->hasMany(Location::class, ['epic_id' => 'epic_id']);
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

    public function getProjects(): ActiveQuery
    {
        return $this->hasMany(Project::class, ['epic_id' => 'epic_id']);
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
     * Determines whether the user can manage epics
     *
     * @throws HttpException
     */
    public static function canUserManageEpic(): bool
    {
        if (Yii::$app->user->can('manageEpic')) {
            return true;
        }

        throw new HttpException(403, Yii::t('app', 'NO_RIGHT_TO_MANAGE_EPIC'));
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
            $aSortPriority = $a->getStatus()->getSortPriority();
            $bSortPriority = $b->getStatus()->getSortPriority();

            if ($aSortPriority === $bSortPriority) {
                return $a->epic_id > $b->epic_id ? -1 : 1; // it impossible to have the same ID and with this, the sorting is deterministic
            }

            return $aSortPriority < $bSortPriority ? -1 : 1;
        });

        return $epics;
    }
}
