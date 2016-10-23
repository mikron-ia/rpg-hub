<?php

namespace common\models;

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
 *
 * @property User $user
 */
class PerformedAction extends ActiveRecord
{
    const PERFORMED_ACTION_CREATE = 'create';
    const PERFORMED_ACTION_UPDATE = 'update';
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
            [['user_id', 'operation', 'class', 'object_id'], 'required'],
            [['user_id', 'object_id', 'performed_at'], 'integer'],
            [['operation', 'class'], 'string', 'max' => 80],
            ['operation', 'in', 'range' => [self::PERFORMED_ACTION_CREATE, self::PERFORMED_ACTION_UPDATE]],
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
     * @param string $operation Operation performed
     * @param string $class Class of the object influenced
     * @param int $object_id ID of the object
     * @return bool Success of the operation
     */
    static public function createRecord($operation, $class, $object_id):bool
    {
        $record = new PerformedAction();

        $record->user_id = Yii::$app->user->identity->getId();
        $record->operation = $operation;
        $record->class = $class;
        $record->object_id = $object_id;

        return $record->save();
    }
}
