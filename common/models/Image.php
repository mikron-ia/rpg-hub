<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\components\service\ImageRotationService;
use common\dto\ImageDisplayObject;
use common\models\core\HasEpicControl;
use common\models\core\HasKey;
use common\models\core\ImageDisplayMode;
use common\models\tools\ToolsForEntity;
use Override;
use Random\RandomException;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\web\HttpException;

/**
 * This is the model class for table "image".
 *
 * @property int $image_id
 * @property int $epic_id
 * @property string $key
 * @property string|null $name
 * @property string|null $note
 * @property string|null $title
 * @property string|null $alt
 * @property int|null $display_height
 * @property int|null $display_width
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Epic $epic
 * @property ImageLink[] $imageLinks
 * @property User $createdBy
 * @property User $updatedBy
 */
class Image extends ActiveRecord implements HasEpicControl, HasKey
{
    use ToolsForEntity;

    #[Override]
    public static function tableName(): string
    {
        return 'image';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'image';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['name', 'note', 'title', 'alt'], 'default', 'value' => null],
            [['epic_id'], 'required'],
            [['epic_id'], 'integer'],
            [['display_height', 'display_width'], 'integer', 'min' => 1],
            [['note'], 'string'],
            [['name', 'title'], 'string', 'max' => 120],
            [['alt'], 'string', 'max' => 255],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'image_id' => Yii::t('app', 'IMAGE_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'IMAGE_KEY'),
            'name' => Yii::t('app', 'IMAGE_NAME'),
            'note' => Yii::t('app', 'IMAGE_NOTE'),
            'title' => Yii::t('app', 'IMAGE_TITLE'),
            'alt' => Yii::t('app', 'IMAGE_ALT'),
            'display_height' => Yii::t('app', 'IMAGE_HEIGHT'),
            'display_width' => Yii::t('app', 'IMAGE_WIDTH'),
            'created_at' => Yii::t('app', 'IMAGE_CREATED_AT'),
            'updated_at' => Yii::t('app', 'IMAGE_UPDATED_AT'),
            'created_by' => Yii::t('app', 'IMAGE_CREATED_BY'),
            'updated_by' => Yii::t('app', 'IMAGE_UPDATED_BY'),
        ];
    }

    #[Override]
    public function attributeHints(): array
    {
        return [
            'name' => Yii::t('app', 'IMAGE_HINT_NAME'),
            'note' => Yii::t('app', 'IMAGE_HINT_NOTE'),
            'title' => Yii::t('app', 'IMAGE_HINT_TITLE'),
            'alt' => Yii::t('app', 'IMAGE_HINT_ALT'),
            'display_height' => Yii::t('app', 'IMAGE_HINT_HEIGHT'),
            'display_width' => Yii::t('app', 'IMAGE_HINT_WIDTH'),
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

    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getImageLinks(): ActiveQuery
    {
        return $this->hasMany(ImageLink::class, ['image_id' => 'image_id']);
    }

    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getNoteFormatted(): string
    {
        return Markdown::process(Html::encode($this->note), 'gfm');
    }

    static public function canUserIndexThem(): bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    static public function canUserCreateThem(): bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    public function canUserControlYou(): bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    public function canUserViewYou(): bool
    {
        return self::canUserViewInEpic($this->epic);
    }

    static function throwExceptionAboutCreate(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_IMAGE'));
    }

    static function throwExceptionAboutControl(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_IMAGE'));
    }

    static function throwExceptionAboutIndex(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_IMAGE'));
    }

    static function throwExceptionAboutView(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_IMAGE'));
    }

    public function provideDisplayableImage(bool $skipAlways = false): ?ImageDisplayObject
    {
        $links = ImageRotationService::filterImageLinks(
            links: $this->imageLinks,
            mode: $skipAlways ? ImageDisplayMode::Backup : ImageDisplayMode::Always
        );

        if (count($links) === 0 && !$skipAlways) {
            // if there are no active links at all, use backup links
            $links = ImageRotationService::filterImageLinks(links: $this->imageLinks, mode: ImageDisplayMode::Backup);
        }

        if (count($links) > 0) {
            try {
                $randomChoice = random_int(0, ImageRotationService::calculateTotalWeight($links) - 1);
            } catch (RandomException) {
                // todo add logging
                $randomChoice = 0;
            }

            return ImageRotationService::makeDisplayObjectWithDimensions(
                image: $this,
                imageLink: ImageRotationService::chooseLink($this->imageLinks, $randomChoice)
            );
        }

        return null;
    }
}
