<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "announcement".
 *
 * @property int $announcement_id
 * @property int|null $epic_id
 * @property string|null $title
 * @property string|null $content
 * @property int|null $visible_from
 * @property int|null $visible_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class Announcement extends ActiveRecord
{

    public static function tableName(): string
    {
        return 'announcement';
    }

    public function rules(): array
    {
        return [
            [['epic_id', 'title', 'content', 'visible_from', 'visible_to'], 'default', 'value' => null],
            [
                ['epic_id', 'visible_from', 'visible_to', 'created_by', 'updated_by', 'created_at', 'updated_at'],
                'integer'
            ],
            [['content'], 'string'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'required'],
            [['title'], 'string', 'max' => 255],
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
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'title' => Yii::t('app', 'ANNOUNCEMENT_TITLE'),
            'content' => Yii::t('app', 'ANNOUNCEMENT_CONTENT'),
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
}
