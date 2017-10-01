<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "task".
 *
 * @property string $task_id
 * @property string $user_id
 * @property string $title
 * @property string $status
 * @property string $details
 *
 * @property User $user
 */
final class Task extends ActiveRecord
{
    public static function tableName()
    {
        return 'task';
    }

    public function rules()
    {
        return [
            [['user_id', 'title', 'status'], 'required'],
            [['user_id'], 'integer'],
            [['details'], 'string'],
            [['title'], 'string', 'max' => 80],
            [['status'], 'string', 'max' => 8],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'task_id' => Yii::t('app', 'TASK_ID'),
            'user_id' => Yii::t('app', 'USER_ID'),
            'title' => Yii::t('app', 'TASK_TITLE'),
            'status' => Yii::t('app', 'TASK_STATUS'),
            'details' => Yii::t('app', 'TASK_DETAILS'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
