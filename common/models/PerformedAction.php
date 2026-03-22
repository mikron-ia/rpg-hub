<?php

namespace common\models;

use common\models\tools\IP;
use common\models\tools\UserAgent;
use Override;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\console\Application as ConsoleApplication;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "performed_action".
 *
 * @property string $id
 * @property string $user_id
 * @property string $operation
 * @property string $class
 * @property string $object_id
 * @property integer $performed_at
 * @property integer $ip_id
 * @property integer $user_agent_id
 *
 * @property IP $ip
 * @property UserAgent $userAgent
 * @property User $user
 */
class PerformedAction extends ActiveRecord
{
    const string PERFORMED_ACTION_CREATE = 'create';
    const string PERFORMED_ACTION_UPDATE = 'update';
    const string PERFORMED_ACTION_LOGIN = 'login';
    const string PERFORMED_ACTION_LOGOUT = 'logout';
    const string PERFORMED_ACTION_MANAGER_ATTACH = 'manager-attach';
    const string PERFORMED_ACTION_MANAGER_DETACH = 'manager-detach';
    const string PERFORMED_ACTION_OTHER = 'other';

    #[Override]
    public static function tableName(): string
    {
        return 'performed_action';
    }

    /**
     * @return array<array<string,string|int|null>>
     */
    #[Override]
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'performed_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['operation'], 'required'],
            [['user_id', 'object_id', 'performed_at', 'ip_id', 'user_agent_id'], 'integer'],
            [['operation', 'class'], 'string', 'max' => 80],
            [
                'operation',
                'in',
                'range' => [
                    self::PERFORMED_ACTION_CREATE,
                    self::PERFORMED_ACTION_UPDATE,
                    self::PERFORMED_ACTION_LOGIN,
                    self::PERFORMED_ACTION_LOGOUT,
                    self::PERFORMED_ACTION_MANAGER_ATTACH,
                    self::PERFORMED_ACTION_MANAGER_DETACH,
                    self::PERFORMED_ACTION_OTHER
                ]
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * @return array<string,string>
     */
    #[Override]
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'PERFORMED_ACTION_ID'),
            'user_id' => Yii::t('app', 'USER_ID'),
            'operation' => Yii::t('app', 'PERFORMED_ACTION_OPERATION'),
            'class' => Yii::t('app', 'PERFORMED_ACTION_CLASS'),
            'object_id' => Yii::t('app', 'PERFORMED_ACTION_OBJECT_ID'),
            'performed_at' => Yii::t('app', 'PERFORMED_ACTION_PERFORMED_AT'),
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getIp(): ActiveQuery
    {
        return $this->hasOne(IP::class, ['id' => 'ip_id']);
    }

    public function getUserAgent(): ActiveQuery
    {
        return $this->hasOne(UserAgent::class, ['id' => 'user_agent_id']);
    }

    /**
     * @param string $operation Operation performed
     * @param string|null $class Class of the object influenced
     * @param int|null $object_id ID of the object
     *
     * @return bool Success of the operation
     *
     * @throws Exception
     */
    public static function createRecord(string $operation, ?string $class, ?int $object_id): bool
    {
        if (Yii::$app instanceof ConsoleApplication) {
            /* There is no point to record an action from a console call */
            return false;
        }

        $record = new PerformedAction();

        $record->user_id = self::makeUserId();
        $record->operation = $operation;
        $record->class = $class;
        $record->object_id = $object_id;
        $record->ip_id = self::makeIp()->id;
        $record->user_agent_id = self::makeAgent()->id;

        return $record->save();
    }

    /**
     * @throws Exception
     */
    public static function createSimplifiedRecord(string $operation): bool
    {
        return self::createRecord($operation, null, null);
    }

    /**
     * @return array<string,string>
     */
    public static function actionNames(): array
    {
        return [
            self::PERFORMED_ACTION_CREATE => Yii::t('app', 'PERFORMED_ACTION_CREATE'),
            self::PERFORMED_ACTION_UPDATE => Yii::t('app', 'PERFORMED_ACTION_UPDATE'),
            self::PERFORMED_ACTION_LOGIN => Yii::t('app', 'PERFORMED_ACTION_LOGIN'),
            self::PERFORMED_ACTION_LOGOUT => Yii::t('app', 'PERFORMED_ACTION_LOGOUT'),
            self::PERFORMED_ACTION_MANAGER_ATTACH => Yii::t('app', 'PERFORMED_ACTION_MANAGER_ATTACH'),
            self::PERFORMED_ACTION_MANAGER_DETACH => Yii::t('app', 'PERFORMED_ACTION_MANAGER_DETACH'),
            self::PERFORMED_ACTION_OTHER => Yii::t('app', 'PERFORMED_ACTION_OTHER'),
        ];
    }

    /**
     * @return string[]
     */
    public static function allowedActions(): array
    {
        return array_keys(self::actionNames());
    }

    public function getName(): string
    {
        $names = self::actionNames();
        return isset($names[$this->operation]) ? $names[$this->operation] : '?';
    }

    /**
     * @throws Exception
     */
    private static function makeAgent(): ?UserAgent
    {
        $userAgentString = Yii::$app->request->getUserAgent();

        $userAgent = UserAgent::findOne(['content' => $userAgentString]);
        if (!$userAgent) {
            $userAgent = new UserAgent(['content' => $userAgentString]);
            $userAgent->save();
            $userAgent->refresh();
        }

        return $userAgent;
    }

    /**
     * @throws Exception
     */
    private static function makeIp(): ?IP
    {
        $ipAddress = Yii::$app->request->userIP;
        $ip = IP::findOne(['content' => $ipAddress]);
        if (!$ip) {
            $ip = new IP(['content' => $ipAddress]);
            $ip->save();
            $ip->refresh();
        }

        return $ip;
    }

    private static function makeUserId(): string|int|null
    {
        /* If there is no user - for example, in the case of registering a new user - assume non-entity by default */
        $userId = null;
        if (!Yii::$app->user->isGuest) {
            /* If there is a user to act, though, get their ID */
            $userId = Yii::$app->user->identity->getId();
        }

        return $userId;
    }
}
