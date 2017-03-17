<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasVisibility;
use common\models\core\Visibility;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "group_membership".
 *
 * @property string $group_character_membership_id
 * @property string $character_id
 * @property string $group_id
 * @property string $visibility
 * @property int $position
 * @property string $short_text
 * @property string $public_text
 * @property string $private_text
 *
 * @property Character $character
 * @property Group $group
 * @property GroupMembershipHistory[] $groupMembershipHistories
 */
class GroupMembership extends ActiveRecord implements HasVisibility
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
            [['short_text'], 'string', 'max' => 80],
            [
                ['character_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Character::className(),
                'targetAttribute' => ['character_id' => 'character_id']
            ],
            [
                ['group_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Group::className(),
                'targetAttribute' => ['group_id' => 'group_id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'group_membership_id' => Yii::t('app', 'GROUP_MEMBERSHIP_ID'),
            'character_id' => Yii::t('app', 'LABEL_CHARACTER'),
            'group_id' => Yii::t('app', 'LABEL_GROUP'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'position' => Yii::t('app', 'LABEL_POSITION'),
            'short_text' => Yii::t('app', 'GROUP_MEMBERSHIP_SHORT_TEXT'),
            'public_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PUBLIC_TEXT'),
            'private_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PRIVATE_TEXT'),
        ];
    }

    public function behaviors()
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::className(),
                'idName' => 'group_membership_id',
                'className' => 'GroupMembership',
            ],
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => ['group_id'],
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacter()
    {
        return $this->hasOne(Character::className(), ['character_id' => 'character_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['group_id' => 'group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroupMembershipHistories()
    {
        return $this->hasMany(GroupMembershipHistory::className(), ['group_membership_id' => 'group_membership_id']);
    }

    static public function allowedVisibilities():array
    {
        return [
            Visibility::VISIBILITY_GM,
            Visibility::VISIBILITY_FULL
        ];
    }

    public function getVisibility():string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getName();
    }

    public function getVisibilityLowercase():string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getNameLowercase();
    }
}
