<?php
namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\Language;
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
 */
final class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const USER_ROLE_NONE = 'none';
    const USER_ROLE_USER = 'user';
    const USER_ROLE_OPERATOR = 'operator';
    const USER_ROLE_MANAGER = 'manager';
    const USER_ROLE_ADMINISTRATOR = 'administrator';

    public $user_role;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function afterFind()
    {
        $this->user_role = $this->getUserRoleCode();
        parent::afterFind();
    }

    public function afterSave($insert, $changedAttributes)
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

    public function behaviors()
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::className(),
                'idName' => 'id',
                'className' => 'User',
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [
                'user_role',
                'in',
                'range' => [
                    self::USER_ROLE_USER,
                    self::USER_ROLE_OPERATOR,
                    self::USER_ROLE_MANAGER,
                    self::USER_ROLE_ADMINISTRATOR
                ]
            ],
            ['language', 'in', 'range' => Language::supportedLanguages()]
        ];
    }

    public function attributeLabels()
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

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by ID
     * @param int $id
     * @return static|null
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token):bool
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

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @return ActiveQuery
     */
    public function getEpicsAssisted():ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_ASSISTANT
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpicsManaged():ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_MANAGER
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpicsGameMasteredAndManaged():ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_GM,
            ParticipantRole::ROLE_MANAGER
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpicsGameMastered():ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_GM
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpicsOperated():ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_GM,
            ParticipantRole::ROLE_ASSISTANT
        ]);
    }
    /**
     * @return ActiveQuery
     */
    public function getEpicsPlayed():ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_PLAYER
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpicsVisible():ActiveQuery
    {
        return $this->getEpicsLimitedByRoles([
            ParticipantRole::ROLE_GM,
            ParticipantRole::ROLE_ASSISTANT,
            ParticipantRole::ROLE_PLAYER,
            ParticipantRole::ROLE_WATCHER
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpics():ActiveQuery
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
    public function getEpicsLimitedByRoles(array $roles):ActiveQuery
    {
        return Epic::find()
            ->joinWith('participants')
            ->joinWith('participants.participantRoles')
            ->where(['user_id' => $this->id, 'role' => $roles]);
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return \string[]
     */
    static public function getAllForDropDown():array
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

    /**
     * @return string[]
     */
    static public function statusNames():array
    {
        return [
            self::STATUS_DELETED => Yii::t('app', 'USER_STATUS_DELETED'),
            self::STATUS_ACTIVE => Yii::t('app', 'USER_STATUS_ACTIVE'),
        ];
    }

    public function getUserRoleName():string
    {
        $names = self::userRoleNames();
        $code = $this->getUserRoleCode();
        return isset($names[$code]) ? $names[$code] : '?';
    }

    /**
     * @return string
     */
    public function getUserRoleCode():string
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
     * @return string[]
     */
    static public function getFullUserList():array
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
     * @return string[]
     */
    static public function userRoleNames():array
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
     * @return string[]
     */
    static public function allowedUserRoleNames():array
    {
        $roles = static::userRoleNames();
        $allowedRoles = static::allowedUserRoles();

        foreach ($roles as $key => $role) {
            if (!in_array($key, $allowedRoles)) {
                unset($roles[$key]);
            }
        }

        return $roles;
    }

    /**
     * @return string[]
     */
    static public function allowedUserRoles():array
    {
        return [self::USER_ROLE_USER, self::USER_ROLE_OPERATOR, self::USER_ROLE_MANAGER];
    }

    /**
     * @return string[]
     */
    static public function operatorUserRoles():array
    {
        return [self::USER_ROLE_OPERATOR, self::USER_ROLE_MANAGER, self::USER_ROLE_ADMINISTRATOR];
    }

    /**
     * @return ActiveQuery
     */
    public function getParticipants()
    {
        return $this->hasMany(Participant::className(), ['user_id' => 'id']);
    }
}
