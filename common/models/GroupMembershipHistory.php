<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "group_character_membership_history".
 *
 * @property string $group_character_membership_history_id
 * @property string $group_character_membership_id
 * @property string $visibility
 * @property string $public_text
 * @property string $private_text
 *
 * @property GroupMembership $groupCharacterMembership
 */
class GroupMembershipHistory extends \yii\db\ActiveRecord
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
            [['visibility'], 'string', 'max' => 20],
            [['group_membership_id'], 'exist', 'skipOnError' => true, 'targetClass' => GroupMembership::className(), 'targetAttribute' => ['group_membership_id' => 'group_character_membership_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'group_membership_history_id' => Yii::t('app', 'GROUP_MEMBERSHIP_HISTORY_ID'),
            'group_membership_id' => Yii::t('app', 'GROUP_MEMBERSHIP'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'public_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PUBLIC_TEXT'),
            'private_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PRIVATE_TEXT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMembership()
    {
        return $this->hasOne(GroupMembership::className(), ['group_membership_id' => 'group_membership_id']);
    }
}
