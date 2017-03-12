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
 * @property GroupCharacterMembership $groupCharacterMembership
 */
class GroupCharacterMembershipHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'group_character_membership_history';
    }

    public function rules()
    {
        return [
            [['group_character_membership_id'], 'required'],
            [['group_character_membership_id'], 'integer'],
            [['public_text', 'private_text'], 'string'],
            [['visibility'], 'string', 'max' => 20],
            [['group_character_membership_id'], 'exist', 'skipOnError' => true, 'targetClass' => GroupCharacterMembership::className(), 'targetAttribute' => ['group_character_membership_id' => 'group_character_membership_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'group_character_membership_history_id' => Yii::t('app', 'Group Character Membership History ID'),
            'group_character_membership_id' => Yii::t('app', 'Group Character Membership ID'),
            'visibility' => Yii::t('app', 'Visibility'),
            'public_text' => Yii::t('app', 'Public Text'),
            'private_text' => Yii::t('app', 'Private Text'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupCharacterMembership()
    {
        return $this->hasOne(GroupCharacterMembership::className(), ['group_character_membership_id' => 'group_character_membership_id']);
    }
}
