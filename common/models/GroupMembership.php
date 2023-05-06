<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasVisibility;
use common\models\core\Visibility;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "group_membership".
 *
 * @property string $group_membership_id
 * @property string $character_id
 * @property string $group_id
 * @property string $visibility
 * @property string $status
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
    const STATUS_ACTIVE = 'active';
    const STATUS_PASSIVE = 'passive';
    const STATUS_PAST = 'past';
    const STATUS_DELETED = 'deleted';

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
            [['status', 'visibility'], 'string', 'max' => 20],
            [['short_text'], 'string', 'max' => 80],
            [
                ['character_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Character::class,
                'targetAttribute' => ['character_id' => 'character_id']
            ],
            [
                ['group_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Group::class,
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
            'status' => Yii::t('app', 'GROUP_MEMBERSHIP_STATUS'),
            'position' => Yii::t('app', 'LABEL_POSITION'),
            'short_text' => Yii::t('app', 'GROUP_MEMBERSHIP_SHORT_TEXT'),
            'public_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PUBLIC_TEXT'),
            'private_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PRIVATE_TEXT'),
        ];
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            $this->createHistoryRecord();
        }

        return parent::beforeSave($insert);
    }

    public function behaviors()
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'group_membership_id',
                'className' => 'GroupMembership',
            ],
            'positionBehavior' => [
                'class' => PositionBehavior::class,
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
        return $this->hasOne(Character::class, ['character_id' => 'character_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::class, ['group_id' => 'group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroupMembershipHistories()
    {
        return $this->hasMany(GroupMembershipHistory::class, ['group_membership_id' => 'group_membership_id']);
    }

    static public function allowedVisibilities(): array
    {
        return [
            Visibility::VISIBILITY_GM,
            Visibility::VISIBILITY_FULL
        ];
    }

    public function getVisibility(): string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getName();
    }

    public function getVisibilityLowercase(): string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getNameLowercase();
    }

    /**
     * @return string|null
     */
    public function getPublicFormatted()
    {
        return Markdown::process(Html::encode($this->public_text), 'gfm');
    }

    /**
     * @return string|null
     */
    public function getPrivateFormatted()
    {
        return Markdown::process(Html::encode($this->private_text), 'gfm');
    }

    /**
     * @return GroupMembershipHistory|null
     */
    public function createHistoryRecord()
    {
        $membership = GroupMembership::findOne(['group_membership_id' => $this->group_membership_id]);

        if (
            ($membership->private_text === $this->private_text) &&
            ($membership->public_text === $this->public_text) &&
            ($membership->status === $this->status) &&
            ($membership->visibility === $this->visibility) &&
            ($membership->short_text === $this->short_text)
        ) {
            return null;
        }

        return GroupMembershipHistory::createFromMembership($membership);
    }

    /**
     * @return string[]
     */
    static public function statusNames(): array
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'MEMBERSHIP_STATUS_ACTIVE'),
            self::STATUS_PASSIVE => Yii::t('app', 'MEMBERSHIP_STATUS_PASSIVE'),
            self::STATUS_PAST => Yii::t('app', 'MEMBERSHIP_STATUS_PAST'),
            self::STATUS_DELETED => Yii::t('app', 'MEMBERSHIP_STATUS_DELETED'),
        ];
    }

    /**
     * @return string[]
     */
    static public function statusClasses(): array
    {
        return [
            self::STATUS_ACTIVE => 'membership-status-active',
            self::STATUS_PASSIVE => 'membership-status-passive',
            self::STATUS_PAST => 'membership-status-past',
            self::STATUS_DELETED => 'membership-status-deleted',
        ];
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        $names = self::statusNames();
        return isset($names[$this->status]) ? $names[$this->status] : '?';
    }

    /**
     * @return string
     */
    public function getStatusClass(): string
    {
        $names = self::statusClasses();
        return isset($names[$this->status]) ? $names[$this->status] : '';
    }
}
