<?php

namespace common\models;

use common\models\core\HasVisibility;
use common\models\tools\ToolsForHasVisibility;
use Override;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Html;
use yii\helpers\Markdown;

/**
 * @property string $group_membership_history_id
 * @property string $group_membership_id
 * @property string $visibility
 * @property string $status
 * @property string $created_at
 * @property string $short_text
 * @property string $public_text
 * @property string $private_text
 *
 * @property GroupMembership $groupCharacterMembership
 */
class GroupMembershipHistory extends ActiveRecord implements HasVisibility
{
    use ToolsForHasVisibility;

    public static function tableName(): string
    {
        return 'group_membership_history';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['group_membership_id'], 'required'],
            [['group_membership_id'], 'integer'],
            [['public_text', 'private_text'], 'string'],
            [['visibility'], 'string', 'max' => 20],
            [
                ['group_membership_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => GroupMembership::class,
                'targetAttribute' => ['group_membership_id' => 'group_membership_id']
            ],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'group_membership_history_id' => Yii::t('app', 'GROUP_MEMBERSHIP_HISTORY_ID'),
            'group_membership_id' => Yii::t('app', 'GROUP_MEMBERSHIP'),
            'created_at' => Yii::t('app', 'LABEL_CREATED_AT'),
            'time_ic' => Yii::t('app', 'LABEL_TIME_IC'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'status' => Yii::t('app', 'LABEL_STATUS'),
            'short_text' => Yii::t('app', 'GROUP_MEMBERSHIP_SHORT_TEXT'),
            'public_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PUBLIC_TEXT'),
            'private_text' => Yii::t('app', 'GROUP_MEMBERSHIP_PRIVATE_TEXT'),
        ];
    }

    #[Override]
    public function behaviors(): array
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
            ],
        ];
    }

    public function getGroupMembership(): ActiveQuery
    {
        return $this->hasOne(GroupMembership::class, ['group_membership_id' => 'group_membership_id']);
    }

    public function getPublicFormatted(): ?string
    {
        return Markdown::process(Html::encode($this->public_text), 'gfm');
    }

    public function getPrivateFormatted(): ?string
    {
        return Markdown::process(Html::encode($this->private_text), 'gfm');
    }

    /**
     * @throws Exception
     */
    public static function createFromMembership(GroupMembership $membership): ?GroupMembershipHistory
    {
        $history = new GroupMembershipHistory();

        $history->group_membership_id = $membership->group_membership_id;
        $history->short_text = $membership->short_text;
        $history->public_text = $membership->public_text;
        $history->private_text = $membership->private_text;
        $history->visibility = $membership->visibility;
        $history->status = $membership->status;

        if ($history->save()) {
            $history->refresh();
            return $history;
        }

        return null;
    }

    /**
     * @return string[]
     */
    public static function statusNames(): array
    {
        return GroupMembership::statusNames();
    }

    /**
     * @return string[]
     */
    public static function statusClasses(): array
    {
        return GroupMembership::statusClasses();
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
