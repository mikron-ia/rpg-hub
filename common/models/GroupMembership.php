<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasKey;
use common\models\core\HasVisibility;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasVisibility;
use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\web\HttpException;
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
 * @property string $key
 * @property string $short_text
 * @property string $public_text
 * @property string $private_text
 *
 * @property Character $character
 * @property Group $group
 * @property GroupMembershipHistory[] $groupMembershipHistories
 *
 * @method movePrev()
 * @method moveNext()
 */
class GroupMembership extends ActiveRecord implements HasVisibility, HasKey
{
    use ToolsForEntity;
    use ToolsForHasVisibility;

    const string STATUS_ACTIVE = 'active';
    const string STATUS_PASSIVE = 'passive';
    const string STATUS_PAST = 'past';
    const string STATUS_DELETED = 'deleted';

    #[Override]
    public static function tableName(): string
    {
        return 'group_membership';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'groupMembership';
    }

    #[Override]
    public function rules(): array
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
            [
                ['visibility'],
                'in',
                'range' => fn() => $this->allowedVisibilitiesForValidator(),
            ],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'group_membership_id' => Yii::t('app', 'GROUP_MEMBERSHIP_ID'),
            'key' => Yii::t('app', 'GROUP_MEMBERSHIP_KEY'),
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

    /**
     * @throws HttpException
     */
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
        }

        if (!$insert) {
            $this->createHistoryRecord();
        }

        return parent::beforeSave($insert);
    }

    #[Override]
    public function behaviors(): array
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

    public function getCharacter(): ActiveQuery
    {
        return $this->hasOne(Character::class, ['character_id' => 'character_id']);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(Group::class, ['group_id' => 'group_id']);
    }

    public function getGroupMembershipHistories(): ActiveQuery
    {
        return $this->hasMany(GroupMembershipHistory::class, ['group_membership_id' => 'group_membership_id']);
    }

    public function getPublicFormatted(): ?string
    {
        return Markdown::process(Html::encode($this->public_text), 'gfm');
    }

    public function getPrivateFormatted(): ?string
    {
        return Markdown::process(Html::encode($this->private_text), 'gfm');
    }

    public function createHistoryRecord(): ?GroupMembershipHistory
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

    public function getStatus(): string
    {
        $names = self::statusNames();
        return isset($names[$this->status]) ? $names[$this->status] : '?';
    }

    public function getStatusClass(): string
    {
        $names = self::statusClasses();
        return isset($names[$this->status]) ? $names[$this->status] : '';
    }
}
