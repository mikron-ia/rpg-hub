<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\Displayable;
use common\models\core\HasDescriptions;
use common\models\core\HasKey;
use common\models\core\HasVisibility;
use common\models\core\Language;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasVisibility;
use common\models\tools\ToolsForLinkTags;
use common\models\type\DescriptionType;
use Error;
use Override;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\StringHelper;
use yii\web\HttpException;
use yii2tech\ar\position\PositionBehavior;

/**
 * @property int $description_id
 * @property int $description_pack_id
 * @property string $key
 * @property string $title
 * @property string $code
 * @property string $public_text
 * @property string|null $protected_text
 * @property string|null $private_text
 * @property string|null $public_text_expanded
 * @property string|null $protected_text_expanded
 * @property string|null $private_text_expanded
 * @property string $lang
 * @property string $visibility
 * @property int|null $position
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int|null $point_in_time_start_id
 * @property int|null $point_in_time_end_id
 * @property int|null $point_in_time_still_valid_id
 * @property int $outdated
 *
 * @property User $createdBy
 * @property DescriptionHistory[] $descriptionHistories
 * @property DescriptionPack $descriptionPack
 * @property PointInTime $pointInTimeStart
 * @property PointInTime $pointInTimeEnd
 * @property PointInTime $pointInTimeStillValid
 * @property User $updatedBy
 *
 * @method movePrev()
 * @method moveNext()
 */
class Description extends ActiveRecord implements Displayable, HasKey, HasVisibility
{
    use ToolsForEntity;
    use ToolsForLinkTags;
    use ToolsForHasVisibility;

    const string CONTROLLING_CLASS_PREFIX = 'common\models\\';

    #[Override]
    public static function tableName(): string
    {
        return 'description';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'description';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [
                [
                    'description_pack_id',
                    'position',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'point_in_time_start_id',
                    'point_in_time_end_id',
                    'point_in_time_still_valid_id',
                ],
                'integer',
            ],
            [['description_pack_id', 'code', 'public_text', 'lang', 'visibility'], 'required'],
            [
                [
                    'public_text',
                    'protected_text',
                    'private_text',
                    'public_text_expanded',
                    'protected_text_expanded',
                    'private_text_expanded',
                ],
                'string',
            ],
            [['code'], 'string', 'max' => 40],
            [['lang'], 'string', 'max' => 5],
            [['visibility'], 'string', 'max' => 20],
            [['outdated'], 'default', 'value' => 0],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::class,
                'targetAttribute' => ['description_pack_id' => 'description_pack_id'],
            ],
            [['code'], 'in', 'range' => fn() => DescriptionType::allowedTypesForValidator()],
            [['visibility'], 'in', 'range' => fn() => $this->allowedVisibilitiesForValidator()],
            [
                ['point_in_time_start_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => PointInTime::class,
                'targetAttribute' => ['point_in_time_start_id' => 'point_in_time_id'],
            ],
            [
                ['point_in_time_end_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => PointInTime::class,
                'targetAttribute' => ['point_in_time_end_id' => 'point_in_time_id'],
            ],
            [
                ['point_in_time_still_valid_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => PointInTime::class,
                'targetAttribute' => ['point_in_time_still_valid_id' => 'point_in_time_id'],
            ],
            [
                ['created_by'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['created_by' => 'id'],
            ],
            [
                ['updated_by'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['updated_by' => 'id'],
            ],
        ];
    }

    /**
     * @throws Exception
     * @throws HttpException
     */
    #[Override]
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
        }

        if (!$insert) {
            $this->createHistoryRecord();
        }

        $this->public_text_expanded = $this->expandText($this->public_text);
        $this->protected_text_expanded = $this->expandText($this->protected_text);
        $this->private_text_expanded = $this->expandText($this->private_text);

        return parent::beforeSave($insert);
    }

    #[Override]
    public function afterSave($insert, $changedAttributes): void
    {
        if (!empty($changedAttributes)) {
            $this->descriptionPack->touch('updated_at');
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return array<string,string>
     */
    #[Override]
    public function attributeHints(): array
    {
        return [
            'public_text' => Yii::t('app', 'DESCRIPTION_HINT_TEXT_PUBLIC'),
            'protected_text' => Yii::t('app', 'DESCRIPTION_HINT_TEXT_PROTECTED'),
            'private_text' => Yii::t('app', 'DESCRIPTION_HINT_TEXT_PRIVATE'),
            'point_in_time_end_id' => Yii::t('app', 'DESCRIPTION_HINT_POINT_IN_TIME_END'),
            'outdated' => Yii::t('app', 'DESCRIPTION_HINT_OUTDATED'),
        ];
    }

    /**
     * @return array<string,string>
     */
    #[Override]
    public function attributeLabels(): array
    {
        return [
            'description_id' => Yii::t('app', 'DESCRIPTION_ID'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
            'key' => Yii::t('app', 'DESCRIPTION_KEY'),
            'title' => Yii::t('app', 'DESCRIPTION_TITLE'),
            'code' => Yii::t('app', 'DESCRIPTION_CODE'),
            'public_text' => Yii::t('app', 'DESCRIPTION_TEXT_PUBLIC'),
            'protected_text' => Yii::t('app', 'DESCRIPTION_TEXT_PROTECTED'),
            'private_text' => Yii::t('app', 'DESCRIPTION_TEXT_PRIVATE'),
            'public_text_expanded' => Yii::t('app', 'DESCRIPTION_TEXT_PUBLIC_EXPANDED'),
            'protected_text_expanded' => Yii::t('app', 'DESCRIPTION_TEXT_PROTECTED_EXPANDED'),
            'private_text_expanded' => Yii::t('app', 'DESCRIPTION_TEXT_PRIVATE_EXPANDED'),
            'lang' => Yii::t('app', 'LABEL_LANGUAGE'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
            'point_in_time_start_id' => Yii::t('app', 'DESCRIPTION_POINT_IN_TIME_START'),
            'point_in_time_end_id' => Yii::t('app', 'DESCRIPTION_POINT_IN_TIME_END'),
            'point_in_time_still_valid_id' => Yii::t('app', 'DESCRIPTION_POINT_IN_TIME_STILL_VALID'),
            'outdated' => Yii::t('app', 'DESCRIPTION_OUTDATED'),
        ];
    }

    #[Override]
    public function behaviors(): array
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::class,
                'positionAttribute' => 'position',
                'groupAttributes' => ['description_pack_id'],
            ],
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'description_id',
                'className' => 'Description',
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'blameableBehavior' => [
                'class' => BlameableBehavior::class,
            ],
        ];
    }

    /**
     * Provides a list of allowed description types for the class the object belongs to
     * The list is provided as full names in the current language and keyed by codes
     *
     * @return string[]
     */
    public function typeNamesForThisClass(): array
    {
        try {
            $object = new (self::CONTROLLING_CLASS_PREFIX . $this->descriptionPack->class);
            if ($object instanceof HasDescriptions) {
                $typesAllowed = $object::allowedDescriptionTypes();
            }
        } catch (Error) {
            // todo Add logging for invalid class on DescriptionPack
            // for now we are satisfied with $typesAllowed being an empty array
        }

        return DescriptionType::typeNames($typesAllowed ?? []);
    }

    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getDescriptionPack(): ActiveQuery
    {
        return $this->hasOne(DescriptionPack::class, ['description_pack_id' => 'description_pack_id']);
    }

    public function getPointInTimeStart(): ActiveQuery
    {
        return $this->hasOne(PointInTime::class, ['point_in_time_id' => 'point_in_time_start_id']);
    }

    public function getPointInTimeEnd(): ActiveQuery
    {
        return $this->hasOne(PointInTime::class, ['point_in_time_id' => 'point_in_time_end_id']);
    }

    public function getPointInTimeStillValid(): ActiveQuery
    {
        return $this->hasOne(PointInTime::class, ['point_in_time_id' => 'point_in_time_still_valid_id']);
    }

    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getTypeName(): string
    {
        return $this->getTypeEnum()?->name() ?? '?';
    }

    public function getTypeDescription(): string
    {
        return $this->getTypeEnum()?->description() ?? '?';
    }

    public function getTypeEnum(): ?DescriptionType
    {
        return DescriptionType::tryFrom($this->code);
    }

    public function getLanguage(): ?string
    {
        $language = Language::create($this->lang);
        return $language->getName();
    }

    public function getPublicFormatted(): string
    {
        return $this->formatText($this->public_text_expanded, true);
    }

    public function getProtectedFormatted(): string
    {
        return $this->formatText($this->protected_text_expanded, true);
    }

    public function getPrivateFormatted(): string
    {
        return $this->formatText($this->private_text_expanded, true);
    }

    /**
     * Provides a simple representation of the object content, fit for basic display in an index or a summary
     *
     * @return array<string,string>
     */
    #[Override]
    public function getSimpleDataForApi(): array
    {
        return [
            'title' => $this->getTypeName(),
        ];
    }

    /**
     * Provides complete representation of public parts of object content, fit for full card display
     *
     * @return array<string,string>
     */
    #[Override]
    public function getCompleteDataForApi(): array
    {
        return [
            'title' => $this->getTypeName(),
            'text' => $this->getPublicFormatted(),
        ];
    }

    #[Override]
    public function isVisibleInApi(): bool
    {
        return ($this->getVisibility() === Visibility::VISIBILITY_FULL);
    }

    /**
     * @throws Exception
     */
    public function createHistoryRecord(): ?DescriptionHistory
    {
        $description = Description::findOne(['description_id' => $this->description_id]);

        if (
            ($description->public_text === $this->public_text) &&
            ($description->protected_text === $this->protected_text) &&
            ($description->private_text === $this->private_text)) {
            return null;
        }

        return DescriptionHistory::createFromDescription($description);
    }

    /**
     * Provides word count that a player can see
     */
    public function getWordCount(): int
    {
        return $this->getWordCountForPublic();
    }

    /**
     * Provides word count for the public part
     */
    public function getWordCountForPublic(): int
    {
        return StringHelper::countWords($this->public_text_expanded);
    }

    /**
     * Provides word count for the protected part
     */
    public function getWordCountForProtected(): int
    {
        return StringHelper::countWords($this->protected_text_expanded);
    }

    /**
     * Provides word count for the private part
     */
    public function getWordCountForPrivate(): int
    {
        return StringHelper::countWords($this->private_text_expanded);
    }
}
