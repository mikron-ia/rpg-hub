<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\Language;
use common\models\core\UserStatus;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $language
 *
 * @property Epic[] $epics
 * @property Epic[] $epicsAssisted
 * @property Epic[] $epicsManaged
 * @property Epic[] $epicsGameMastered
 * @property Epic[] $epicsGameMasteredAndManaged
 * @property Epic[] $epicsOperated
 * @property Epic[] $epicsPlayed
 * @property Epic[] $epicsVisible
 * @property Participant[] $participants
 * @property Task[] $tasks
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const USER_ROLE_NONE = 'none';
    public const USER_ROLE_USER = 'user';
    public const USER_ROLE_OPERATOR = 'operator';
    public const USER_ROLE_MANAGER = 'manager';
    public const USER_ROLE_ADMINISTRATOR = 'administrator';

    public ?string $user_role = null;

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function afterFind(): void
    {
        $this->user_role = $this->getUserRoleCode();
        parent::afterFind();
    }

    public function afterSave($insert, $changedAttributes): void
    {
        if (!$this->hasErrors()) {
            $roleCode = $this->getUserRoleCode();

            if ($this->user_role != $roleCode && $roleCode != self::USER_ROLE_ADMINISTRATOR) {
                $auth = Yii::$app->authManager;

                if ($auth->checkAccess($this->id, $roleCode)) {
                    $auth->revoke($auth->getRole($roleCode), $this->id);
                }
                $auth->assign($auth->getRole($this->user_role), $this->id);
                $this->touch('updated_at');
            }

        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function behaviors(): array
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'id',
                'className' => 'User',
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['status', 'default', 'value' => UserStatus::Active->value],
            [
                'status',
                'in',
                'range' => [
                    UserStatus::Forgotten->value,
                    UserStatus::Deleted->value,
                    UserStatus::Disabled->value,
                    UserStatus::Active->value,
                ],
            ],
            [
                'user_role',
                'in',
                'range' => [
                    self::USER_ROLE_USER,
                    self::USER_ROLE_OPERATOR,
                    self::USER_ROLE_MANAGER,
                    self::USER_ROLE_ADMINISTRATOR,
                ]
            ],
            ['language', 'in', 'range' => Language::supportedLanguages()]
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('app', 'USER_EMAIL'),
            'created_at' => Yii::t('app', 'USER_CREATED_AT'),
            'updated_at' => Yii::t('app', 'USER_UPDATED_AT'),
            'language' => Yii::t('app', 'USER_LANGUAGE'),
            'status' => Yii::t('app', 'USER_STATUS'),
            'username' => Yii::t('app', 'USER_USERNAME'),
            'user_role' => Yii::t('app', 'USER_ROLE'),
        ];
    }

    public static function findIdentity($id): User|IdentityInterface|null
    {
        return static::findOne(['id' => $id, 'status' => UserStatus::Active->value]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by ID
     */
    public static function findById(int $id): null|static
    {
        return static::findOne(['id' => $id, 'status' => UserStatus::Active->value]);
    }

    /**
     * Finds user by username
     */
    public static function findByUsername(string $username): ?User
    {
        return User::findOne(['username' => $username, 'status' => UserStatus::Active->value]);
    }

    /**
     * Finds user by password reset token
     */
    public static function findByPasswordResetToken(string $token): ?User
    {
        if (!User::isPasswordResetTokenValid($token)) {
            return null;
        }

        return User::findOne([
            'password_reset_token' => $token,
            'status' => UserStatus::Active->value,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     */
    public static function isPasswordResetTokenValid(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function getEpicsAssisted(): ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_ASSISTANT
        ]);
    }

    public function getEpicsManaged(): ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_MANAGER
        ]);
    }

    public function getEpicsGameMasteredAndManaged(): ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_GM,
            ParticipantRole::ROLE_MANAGER
        ]);
    }

    public function getEpicsGameMastered(): ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_GM
        ]);
    }

    public function getEpicsOperated(): ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_GM,
            ParticipantRole::ROLE_ASSISTANT
        ]);
    }

    public function getEpicsPlayed(): ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_PLAYER
        ]);
    }

    public function getEpicsVisible(): ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_GM,
            ParticipantRole::ROLE_ASSISTANT,
            ParticipantRole::ROLE_PLAYER,
            ParticipantRole::ROLE_WATCHER
        ]);
    }

    public function getEpics(): ActiveQuery
    {

        return Epic::find()
            ->joinWith('participants')
            ->joinWith('participants.participantRoles')
            ->where(['user_id' => $this->id]);
    }

    /**
     * @param array $roles
     * @return ActiveQuery
     */
    public function getEpicsLimitedByRoles(array $roles): ActiveQuery
    {
        return Epic::find()
            ->joinWith('participants')
            ->joinWith('participants.participantRoles')
            ->where(['user_id' => $this->id, 'role' => $roles]);
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }

    /**
     * @return array<int,string>
     */
    static public function getAllForDropDown(): array
    {
        /** @var User[] $users */
        $users = User::find()->all();

        /** @var string[] $list */
        $list = [];

        foreach ($users as $user) {
            $list[$user->id] = $user->username;
        }

        return $list;
    }

    public function getUserRoleName(): string
    {
        return (self::userRoleNames())[$this->getUserRoleCode()] ?? '?';
    }

    public function getUserRoleCode(): string
    {
        if (Yii::$app->authManager->checkAccess($this->id, 'administrator')) {
            return self::USER_ROLE_ADMINISTRATOR;
        } elseif (Yii::$app->authManager->checkAccess($this->id, 'manager')) {
            return self::USER_ROLE_MANAGER;
        } elseif (Yii::$app->authManager->checkAccess($this->id, 'operator')) {
            return self::USER_ROLE_OPERATOR;
        } elseif (Yii::$app->authManager->checkAccess($this->id, 'user')) {
            return self::USER_ROLE_USER;
        } else {
            return self::USER_ROLE_NONE;
        }
    }

    /**
     * @return array<int,string>
     */
    static public function getFullUserList(): array
    {
        /**
         * @var $usersUnordered User[]
         */
        $usersUnordered = User::find()->all();
        $userOrdered = [];

        foreach ($usersUnordered as $user) {
            $userOrdered[$user->id] = $user->username;
        }

        return $userOrdered;
    }

    /**
     * @return array<string,string>
     */
    static public function userRoleNames(): array
    {
        return [
            self::USER_ROLE_NONE => Yii::t('app', 'USER_ROLE_NONE'),
            self::USER_ROLE_USER => Yii::t('app', 'USER_ROLE_USER'),
            self::USER_ROLE_OPERATOR => Yii::t('app', 'USER_ROLE_OPERATOR'),
            self::USER_ROLE_MANAGER => Yii::t('app', 'USER_ROLE_MANAGER'),
            self::USER_ROLE_ADMINISTRATOR => Yii::t('app', 'USER_ROLE_ADMINISTRATOR'),
        ];
    }

    /**
     * @return array<string,string>
     */
    static public function allowedUserRoleNames(): array
    {
        $roles = User::userRoleNames();
        $allowedRoles = User::allowedUserRoles();

        foreach ($roles as $key => $role) {
            if (!in_array($key, $allowedRoles)) {
                unset($roles[$key]);
            }
        }

        return $roles;
    }

    /**
     * @return array<int,string>
     */
    static public function allowedUserRoles(): array
    {
        return [self::USER_ROLE_USER, self::USER_ROLE_OPERATOR, self::USER_ROLE_MANAGER];
    }

    /**
     * @return array<int,string>
     */
    static public function operatorUserRoles(): array
    {
        return [self::USER_ROLE_OPERATOR, self::USER_ROLE_MANAGER, self::USER_ROLE_ADMINISTRATOR];
    }

    public function hasProtectedRole(): bool
    {
        return $this->user_role === self::USER_ROLE_ADMINISTRATOR;
    }

    public function getParticipants(): ActiveQuery
    {
        return $this->hasMany(Participant::class, ['user_id' => 'id']);
    }

    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Task::class, ['user_id' => 'id']);
    }
}
