<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasKey;
use common\models\core\ImageDisplayMode;
use common\models\tools\ToolsForEntity;
use Override;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use Yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\HttpException;

/**
 * @property int $image_link_id
 * @property int $image_id
 * @property string $key
 * @property string $link
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
class ImageLink extends ActiveRecord implements HasKey
{
    use ToolsForEntity;

    public const string DEFAULT_WEIGHT = '100';

    #[Override]
    public static function tableName(): string
    {
        return 'image_link';
    }

    public static function keyParameterName(): string
    {
        return 'imageLink';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['display_mode'], 'default', 'value' => ImageDisplayMode::Always->value],
            [['display_weight'], 'default', 'value' => self::DEFAULT_WEIGHT],
            [['link'], 'required'],
            [['link'], 'url'],
            [['display_weight'], 'integer'],
            [['link'], 'string', 'max' => 255],
            [['display_mode'], 'string', 'max' => 6],
            [['display_mode'], 'in', 'range' => fn() => ImageDisplayMode::allowedValues()],
            [
                ['image_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Image::class,
                'targetAttribute' => ['image_id' => 'image_id'],
            ],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'image_link_id' => Yii::t('app', 'IMAGE_LINK_ID'),
            'image_id' => Yii::t('app', 'LABEL_IMAGE'),
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

    /**
     * @throws HttpException
     */
    #[Override]
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
        }

        return parent::beforeSave($insert);
    }

    #[Override]
    public function behaviors(): array
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'image_id',
                'className' => 'Image',
            ],
            'blameableBehavior' => ['class' => BlameableBehavior::class],
            'timestampBehavior' => ['class' => TimestampBehavior::class],
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

    public function getDisplayModeObject(): ImageDisplayMode
    {
        return ImageDisplayMode::from($this->display_mode);
    }
}
