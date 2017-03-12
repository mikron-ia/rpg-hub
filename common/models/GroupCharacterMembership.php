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
 * @property GroupCharacterMembershipHistory[] $groupCharacterMembershipHistories
 */
class GroupCharacterMembership extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'group_character_membership';
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
            'group_character_membership_id' => Yii::t('app', 'Group Character Membership ID'),
            'character_id' => Yii::t('app', 'Character ID'),
            'group_id' => Yii::t('app', 'Group ID'),
            'visibility' => Yii::t('app', 'Visibility'),
            'public_text' => Yii::t('app', 'Public Text'),
            'private_text' => Yii::t('app', 'Private Text'),
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
    public function getGroupCharacterMembershipHistories()
    {
        return $this->hasMany(GroupCharacterMembershipHistory::className(), ['group_character_membership_id' => 'group_character_membership_id']);
    }
}
