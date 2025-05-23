<?php

namespace common\models;

use common\models\core\HasVisibility;
use common\models\core\Visibility;
use common\models\tools\ToolsForHasVisibility;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;

/**
 * This is the model class for table "group_membership_history".
 *
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

    public static function tableName()
    {
        return 'group_membership_history';
    }

    public function rules()
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

    public function attributeLabels()
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

    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getGroupMembership()
    {
        return $this->hasOne(GroupMembership::class, ['group_membership_id' => 'group_membership_id']);
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
     * @param GroupMembership $membership
     * @return GroupMembershipHistory
     */
    static public function createFromMembership(GroupMembership $membership)
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
        } else {
            return null;
        }
    }

    /**
     * @return string[]
     */
    static public function statusNames(): array
    {
        return GroupMembership::statusNames();
    }

    /**
     * @return string[]
     */
    static public function statusClasses(): array
    {
        return GroupMembership::statusClasses();
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
