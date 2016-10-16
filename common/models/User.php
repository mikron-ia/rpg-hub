<?php
namespace common\models;

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
 * @property Epic[] $epicsGameMastered
 * @property Epic[] $epicsPlayed
 */
final class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['language', 'in', 'range' => Language::supportedLanguages()],
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
     *
     * @param int $id
     * @return static|null
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
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
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
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
    public function getEpicsGameMastered()
    {
        return $this->hasMany(Epic::className(), ['epic_id' => 'epic_id'])->viaTable(
            'participant',
            ['user_id' => 'id']
        )->viaTable(
            'participant_role',
            ['participant_id' => 'participant_id'],
            function (ActiveQuery $query) {
                return $query->onCondition("role = 'gm'");
            }
        );
    }

    /**
     * @return ActiveQuery
     */
    public function getEpicsPlayed()
    {
        return $this->hasMany(Epic::className(), ['epic_id' => 'epic_id'])->viaTable(
            'participant',
            ['user_id' => 'id']
        )->viaTable(
            'participant_role',
            ['participant_id' => 'participant_id'],
            function (ActiveQuery $query) {
                return $query->onCondition("role = 'player'");
            }
        );
    }

    /**
     * @return ActiveQuery
     */
    public function getEpics()
    {
        return $this->hasMany(Epic::className(), ['epic_id' => 'epic_id'])->viaTable('participant',
            ['user_id' => 'id']);
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
    static public function getAllForDropDown()
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
    static public function statusNames()
    {
        return [
            self::STATUS_DELETED => Yii::t('app', 'USER_STATUS_DELETED'),
            self::STATUS_ACTIVE => Yii::t('app', 'USER_STATUS_ACTIVE'),
        ];
    }

    /**
     * @return string
     */
    public function getUserRoleName()
    {
        if (Yii::$app->authManager->checkAccess($this->id, 'administrator')) {
            return Yii::t('app', 'USER_ROLE_ADMINISTRATOR');
        } elseif (Yii::$app->authManager->checkAccess($this->id, 'manager')) {
            return Yii::t('app', 'USER_ROLE_MANAGER');
        } elseif (Yii::$app->authManager->checkAccess($this->id, 'operator')) {
            return Yii::t('app', 'USER_ROLE_OPERATOR');
        } elseif (Yii::$app->authManager->checkAccess($this->id, 'user')) {
            return Yii::t('app', 'USER_ROLE_USER');
        } else {
            return Yii::t('app', 'USER_ROLE_NONE');
        }
    }
}
