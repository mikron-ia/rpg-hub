<?php

namespace common\models;

use Override;
use Yii;
use Yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $image_link_id
 * @property int $image_id
 * @property string $link
 * @property string|null $title
 * @property string|null $alt
 * @property string $display_mode
 * @property int $display_weight
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property User $createdBy
 * @property Image $image
 * @property User $updatedBy
 */
class ImageLink extends ActiveRecord
{
    #[Override]
    public static function tableName(): string
    {
        return 'image_link';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['title', 'alt'], 'default', 'value' => null],
            [['display_mode'], 'default', 'value' => 'always'],
            [['display_weight'], 'default', 'value' => 100],
            [['image_id', 'link', 'updated_at', 'modified_at', 'created_by', 'modified_by'], 'required'],
            [['image_id', 'display_weight', 'updated_at', 'modified_at', 'created_by', 'modified_by'], 'integer'],
            [['note'], 'string'],
            [['link', 'alt'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 120],
            [['display_mode'], 'string', 'max' => 6],
            [['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::class, 'targetAttribute' => ['image_id' => 'image_id']],
        ];
    }

    #[Override]
    public function attributeLabels():array
    {
        return [
            'image_link_id' => Yii::t('app', 'IMAGE_LINK_ID'),
            'image_id' => Yii::t('app', 'IMAGE_ID'),
            'link' => Yii::t('app', 'IMAGE_LINK_LINK'),
            'display_mode' => Yii::t('app', 'IMAGE_LINK_DISPLAY_MODE'),
            'display_weight' => Yii::t('app', 'IMAGE_LINK_DISPLAY_WEIGHT'),
            'created_at' => Yii::t('app', 'IMAGE_LINK_CREATED_AT'),
            'updated_at' => Yii::t('app', 'IMAGE_LINK_UPDATED_AT'),
            'created_by' => Yii::t('app', 'IMAGE_LINK_CREATED_BY'),
            'updated_by' => Yii::t('app', 'IMAGE_LINK_UPDATED_BY'),
        ];
    }

    #[Override]
    public function attributeHints(): array
    {
        return [
            'display_mode' => Yii::t('app', 'IMAGE_LINK_HINT_DISPLAY_MODE'),
            'display_weight' => Yii::t('app', 'IMAGE_LINK_HINT_DISPLAY_WEIGHT'),
        ];
    }

    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getImage(): ActiveQuery
    {
        return $this->hasOne(Image::class, ['image_id' => 'image_id']);
    }

    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'modified_by']);
    }
}
