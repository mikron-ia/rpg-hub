<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "group_membership_history".
 *
 * @property string $group_character_membership_history_id
 * @property string $group_character_membership_id
 * @property string $visibility
 * @property string $public_text
 * @property string $private_text
 *
 * @property GroupMembership $groupCharacterMembership
 */
class GroupMembershipHistory extends ActiveRecord
{
    public static function tableName()
    {
        return 'group_membership_history';
    }

    public function rules()
    {
        return [
            [['group_character_membership_id'], 'required'],
            [['group_character_membership_id'], 'integer'],
            [['public_text', 'private_text'], 'string'],
            [['time_ic'], 'string', 'max' => 255],
            [['visibility'], 'string', 'max' => 20],
            [
                ['group_membership_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => GroupMembership::className(),
                'targetAttribute' => ['group_membership_id' => 'group_character_membership_id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'group_membership_history_id' => Yii::t('app', 'GROUP_MEMBERSHIP_HISTORY_ID'),
            'group_membership_id' => Yii::t('app', 'GROUP_MEMBERSHIP'),
            'created_at' => Yii::t('app', 'LABEL_CREATED_AT'),
            'time_ic' => Yii::t('app', 'LABEL_TIME_IC'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'short_text' => Yii::t('app', 'GROUP_MEMBERSHIP_SHORT_TEXT'),
            'public_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PUBLIC_TEXT'),
            'private_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PRIVATE_TEXT'),
        ];
    }

    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null,
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getGroupMembership()
    {
        return $this->hasOne(GroupMembership::className(), ['group_membership_id' => 'group_membership_id']);
    }
}
