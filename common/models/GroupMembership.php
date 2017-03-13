<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "group_character_membership".
 *
 * @property string $group_character_membership_id
 * @property string $character_id
 * @property string $group_id
 * @property string $visibility
 * @property string $public_text
 * @property string $private_text
 *
 * @property Character $character
 * @property Group $group
 * @property GroupMembershipHistory[] $groupMembershipHistories
 */
class GroupMembership extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'group_membership';
    }

    public function rules()
    {
        return [
            [['character_id', 'group_id'], 'required'],
            [['character_id', 'group_id'], 'integer'],
            [['public_text', 'private_text'], 'string'],
            [['visibility'], 'string', 'max' => 20],
            [['character_id'], 'exist', 'skipOnError' => true, 'targetClass' => Character::className(), 'targetAttribute' => ['character_id' => 'character_id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'group_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'group_character_membership_id' => Yii::t('app', 'GROUP_MEMBERSHIP_ID'),
            'character_id' => Yii::t('app', 'LABEL_CHARACTER'),
            'group_id' => Yii::t('app', 'LABEL_GROUP'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'public_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PUBLIC_TEXT'),
            'private_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PRIVATE_TEXT'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacter()
    {
        return $this->hasOne(Character::className(), ['character_id' => 'character_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['group_id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMembershipHistories()
    {
        return $this->hasMany(GroupMembershipHistory::className(), ['group_membership_id' => 'group_membership_id']);
    }
}