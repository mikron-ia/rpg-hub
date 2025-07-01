<?php

namespace common\models;

use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForLinkTags;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;

/**
 * This is the model class for table "announcement".
 *
 * @property int $announcement_id
 * @property string $key
 * @property int|null $epic_id
 * @property string $title
 * @property string $text_raw
 * @property string $text_ready
 * @property int|null $visible_from
 * @property int|null $visible_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Epic $epic
 * @property User $createdBy
 * @property User $updatedBy
 */
class Announcement extends ActiveRecord
{
    use ToolsForEntity;
    use ToolsForLinkTags;

    public static function tableName(): string
    {
        return 'announcement';
    }

    public function rules(): array
    {
        return [
            [['epic_id', 'visible_from', 'visible_to'], 'default', 'value' => null],
            [['title', 'text_raw'], 'required'],
            [['epic_id'], 'integer'],
            [['visible_from', 'visible_to'], 'safe'],
            [['text_raw'], 'string'],
            [['title'], 'string', 'max' => 120],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id'],
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'announcement_id' => Yii::t('app', 'ANNOUNCEMENT_ID'),
            'key' => Yii::t('app', 'ANNOUNCEMENT_KEY'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'title' => Yii::t('app', 'ANNOUNCEMENT_TITLE'),
            'text_raw' => Yii::t('app', 'ANNOUNCEMENT_TEXT_RAW'),
            'text_ready' => Yii::t('app', 'ANNOUNCEMENT_TEXT_READY'),
            'visible_from' => Yii::t('app', 'ANNOUNCEMENT_VISIBLE_FROM'),
            'visible_to' => Yii::t('app', 'ANNOUNCEMENT_VISIBLE_TO'),
            'created_by' => Yii::t('app', 'ANNOUNCEMENT_CREATED_BY'),
            'updated_by' => Yii::t('app', 'ANNOUNCEMENT_UPDATED_BY'),
            'created_at' => Yii::t('app', 'ANNOUNCEMENT_CREATED_AT'),
            'updated_at' => Yii::t('app', 'ANNOUNCEMENT_UPDATED_AT'),
        ];
    }

    public function behaviors(): array
    {
        return [['class' => TimestampBehavior::class], ['class' => BlameableBehavior::class]];
    }

    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey('announcement');
        }

        $this->text_ready = Markdown::process(Html::encode($this->processAllInOrder($this->text_raw)), 'gfm');

        return parent::beforeSave($insert);
    }

    public function  getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
}
