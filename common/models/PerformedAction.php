<?php

namespace common\models;

use common\models\tools\IP;
use common\models\tools\UserAgent;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
    const PERFORMED_ACTION_CREATE = 'create';
    const PERFORMED_ACTION_UPDATE = 'update';
    const PERFORMED_ACTION_LOGIN = 'login';
    const PERFORMED_ACTION_LOGOUT = 'logout';
    const PERFORMED_ACTION_OTHER = 'other';

    public static function tableName()
    {
        return 'performed_action';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'performed_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'operation'], 'required'],
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
                    self::PERFORMED_ACTION_OTHER
                ]
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    public function attributeLabels()
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

    /**
     * @return ActiveQuery
     */
    public function getUser():ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getIp()
    {
        return $this->hasOne(IP::className(), ['id' => 'ip_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserAgent()
    {
        return $this->hasOne(UserAgent::className(), ['id' => 'user_agent_id']);
    }

    /**
     * @param string $operation Operation performed
     * @param string $class Class of the object influenced
     * @param int $object_id ID of the object
     * @return bool Success of the operation
     */
    static public function createRecord($operation, $class, $object_id):bool
    {
        $record = new PerformedAction();

        $ipAddress = Yii::$app->request->userIP;
        $ip = IP::findOne(['content' => $ipAddress]);
        if (!$ip) {
            $ip = new IP(['content' => $ipAddress]);
            $ip->save();
            $ip->refresh();
        }

        $userAgentString = Yii::$app->request->getUserAgent();

        $userAgent = UserAgent::findOne(['content' => $userAgentString]);
        if (!$userAgent) {
            $userAgent = new UserAgent(['content' => $userAgentString]);
            $userAgent->save();
            $userAgent->refresh();
        }

        $record->user_id = Yii::$app->user->identity->getId();
        $record->operation = $operation;
        $record->class = $class;
        $record->object_id = $object_id;
        $record->ip_id = $ip->id;
        $record->user_agent_id = $userAgent->id;

        return $record->save();
    }

    /**
     * @param string $operation Operation performed
     * @return bool Success of the operation
     */
    static public function createSimplifiedRecord($operation):bool
    {
        return self::createRecord($operation, null, null);
    }

    /**
     * @return string[]
     */
    static public function actionNames():array
    {
        return [
            self::PERFORMED_ACTION_CREATE => Yii::t('app', 'PERFORMED_ACTION_CREATE'),
            self::PERFORMED_ACTION_UPDATE => Yii::t('app', 'PERFORMED_ACTION_UPDATE'),
            self::PERFORMED_ACTION_LOGIN => Yii::t('app', 'PERFORMED_ACTION_LOGIN'),
            self::PERFORMED_ACTION_LOGOUT => Yii::t('app', 'PERFORMED_ACTION_LOGOUT'),
            self::PERFORMED_ACTION_OTHER => Yii::t('app', 'PERFORMED_ACTION_OTHER'),
        ];
    }

    /**
     * @return string[]
     */
    static public function allowedActions():array
    {
        return array_keys(self::actionNames());
    }

    /**
     * @return string
     */
    public function getName():string
    {
        $names = self::actionNames();
        return isset($names[$this->operation]) ? $names[$this->operation] : '?';
    }
}
