<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_epics".
 *
 * @property integer $user_epic_id
 * @property string $user_id
 * @property string $epic_id
 * @property string $role
 *
 * @property User $user
 * @property Epic $epic
 */
class UserEpic extends \yii\db\ActiveRecord
{
    const ROLE_GM = 'gm';
    const ROLE_PLAYER = 'player';
    const ROLE_MEMBER = 'member';
    const ROLE_WATCHER = 'watcher';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_epics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'epic_id'], 'required'],
            [['user_id', 'epic_id'], 'integer'],
            [['role'], 'string', 'max' => 20],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['epic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Epic::className(), 'targetAttribute' => ['epic_id' => 'epic_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_epic_id' => Yii::t('app', 'USER_EPIC_ID'),
            'user_id' => Yii::t('app', 'USER_LABEL'),
            'epic_id' => Yii::t('app', 'EPIC_LABEL'),
            'role' => Yii::t('app', 'USER_EPIC_ROLE'),
        ];
    }

    /**
     * @return string[]
     */
    static public function roleNames()
    {
        return [
            self::ROLE_GM => Yii::t('app', 'USER_EPIC_ROLE_GM'),
            self::ROLE_PLAYER => Yii::t('app', 'USER_EPIC_ROLE_PLAYER'),
            self::ROLE_MEMBER => Yii::t('app', 'USER_EPIC_ROLE_MEMBER'),
            self::ROLE_WATCHER => Yii::t('app', 'USER_EPIC_ROLE_WATCHER'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    public function getRoleDescribed()
    {
        $names = self::roleNames();
        if (isset($names[$this->role])) {
            return $names[$this->role];
        } else {
            return "?";
        }
    }
}
