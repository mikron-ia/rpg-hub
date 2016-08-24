<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_action".
 *
 * @property string $user_action_id
 * @property string $user_id
 * @property string $operation
 * @property string $class
 * @property string $id
 *
 * @property User $user
 */
class UserAction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'operation', 'class', 'id'], 'required'],
            [['user_id', 'id'], 'integer'],
            [['operation', 'class'], 'string', 'max' => 80],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_action_id' => Yii::t('app', 'USER_ACTION_ID'),
            'user_id' => Yii::t('app', 'USER_ID'),
            'operation' => Yii::t('app', 'USER_ACTION_OPERATION'),
            'class' => Yii::t('app', 'USER_ACTION_CLASS'),
            'id' => Yii::t('app', 'USER_ACTION_OBJECT_ID'),
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
