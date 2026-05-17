<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasDescriptions;
use common\models\core\HasEpicControl;
use common\models\core\HasImportance;
use common\models\core\HasImportanceCategory;
use common\models\core\HasKey;
use common\models\core\HasScribbles;
use common\models\core\HasSightings;
use common\models\core\HasVisibility;
use common\models\core\ImportanceCategory;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasDescriptions;
use common\models\tools\ToolsForHasScribbles;
use common\models\tools\ToolsForHasVisibility;
use DateTimeImmutable;
use Override;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * @property string $location_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $tagline
 * @property string $visibility
 * @property string $importance_category
 * @property int $updated_at
 * @property int $modified_at
 * @property int|null $description_pack_id
 * @property int|null $importance_pack_id
 * @property int|null $scribble_pack_id
 * @property int|null $seen_pack_id
 * @property int|null $utility_bag_id
 *
 * @property Epic $epic
 * @property DescriptionPack $descriptionPack
 * @property ImportancePack $importancePack
 * @property ScribblePack $scribblePack
 * @property SeenPack $seenPack
 * @property UtilityBag $utilityBag
 */
class Location extends ActiveRecord implements HasEpicControl, HasDescriptions, HasImportance, HasImportanceCategory, HasScribbles, HasSightings, HasVisibility, HasKey
{
    use ToolsForEntity;
    use ToolsForHasDescriptions;
    use ToolsForHasScribbles;
    use ToolsForHasVisibility;

    public bool $is_off_the_record_change = false;

    #[Override]
    public static function tableName(): string
    {
        return 'location';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'location';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['epic_id', 'name'], 'required'],
            [['epic_id'], 'integer'],
            [['name', 'tagline'], 'string', 'max' => 120],
            [['visibility', 'importance_category'], 'string', 'max' => 20],
            [['is_off_the_record_change'], 'boolean'],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [['visibility'], 'in', 'range' => fn() => $this->allowedVisibilitiesForValidator()],
        ];
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function afterFind(): void
    {
        if ($this->seen_pack_id) {
            $this->seenPack->recordNotification();
        }

        parent::afterFind();
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'location_id' => Yii::t('app', 'LOCATION_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'LOCATION_KEY'),
            'name' => Yii::t('app', 'LOCATION_NAME'),
            'tagline' => Yii::t('app', 'LOCATION_TAGLINE'),
            'visibility' => Yii::t('app', 'LOCATION_VISIBILITY'),
            'importance_category' => Yii::t('app', 'LOCATION_IMPORTANCE'),
            'updated_at' => Yii::t('app', 'LOCATION_UPDATED_AT'),
            'modified_at' => Yii::t('app', 'LOCATION_MODIFIED_AT'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
            'importance_pack_id' => Yii::t('app', 'IMPORTANCE_PACK'),
            'scribble_pack_id' => Yii::t('app', 'SCRIBBLE_PACK'),
            'seen_pack_id' => Yii::t('app', 'SEEN_PACK'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
            'is_off_the_record_change' => Yii::t('app', 'CHECK_OFF_THE_RECORD_CHANGE'),
        ];
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function afterSave($insert, $changedAttributes): void
    {
        if (!$this->is_off_the_record_change) {
            $this->seenPack->updateRecord();
        }
        $this->importancePack->flagForRecalculation();

        parent::afterSave($insert, $changedAttributes);
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

        if (empty($this->description_pack_id)) {
            $pack = DescriptionPack::create('Location');
            $this->description_pack_id = $pack->description_pack_id;
        }

        if (empty($this->importance_pack_id)) {
            $pack = ImportancePack::create('Location');
            $this->importance_pack_id = $pack->importance_pack_id;
        }

        if (empty($this->scribble_pack_id)) {
            $pack = ScribblePack::create('Location');
            $this->scribble_pack_id = $pack->scribble_pack_id;
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('Location');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('Location');
            $this->utility_bag_id = $pack->utility_bag_id;
        }

        if (!$this->is_off_the_record_change) {
            $this->modified_at = time();
        }

        return parent::beforeSave($insert);
    }

    #[Override]
    public function behaviors(): array
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'location_id',
                'className' => 'Location',
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => null,
            ],
        ];
    }

    static public function allowedDescriptionTypes(): array
    {
        return [
            Description::TYPE_WHO,
            Description::TYPE_APPEARANCE,
            Description::TYPE_LOCATION,
            Description::TYPE_HISTORY,
            Description::TYPE_ASPECTS,
            Description::TYPE_FACTIONS,
            Description::TYPE_HISTORY,
            Description::TYPE_INTERACTIONS,
            Description::TYPE_RESOURCES,
            Description::TYPE_REPUTATION,
            Description::TYPE_RETINUE,
            Description::TYPE_RUMOURS,
            Description::TYPE_STORIES,
            Description::TYPE_THREADS,
            Description::TYPE_COMMENTARY,
        ];
    }


    public function getDescriptionPack(): ActiveQuery
    {
        return $this->hasOne(DescriptionPack::class, ['description_pack_id' => 'description_pack_id']);
    }

    #[Override]
    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    public function getImportancePack(): ActiveQuery
    {
        return $this->hasOne(ImportancePack::class, ['importance_pack_id' => 'importance_pack_id']);
    }

    public function getScribblePack(): ActiveQuery|ScribblePackQuery
    {
        return $this->hasOne(ScribblePack::class, ['scribble_pack_id' => 'scribble_pack_id']);
    }

    public function getMasterLocation(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['location_id' => 'master_location_id']);
    }

    public function getSubLocations(): ActiveQuery
    {
        return $this->hasMany(Location::class, ['master_location_id' => 'location_id']);
    }

    public function getSeenPack(): ActiveQuery
    {
        return $this->hasOne(SeenPack::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getUtilityBag(): ActiveQuery
    {
        return $this->hasOne(UtilityBag::class, ['utility_bag_id' => 'utility_bag_id']);
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
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_LOCATION'));
    }

    static function throwExceptionAboutControl(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_LOCATION'));
    }

    static function throwExceptionAboutIndex(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_LOCATION'));
    }

    static function throwExceptionAboutView(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_LOCATION'));
    }

    #[Override]
    public function recordSighting(): bool
    {
        return $this->seenPack->recordSighting(importancePack: $this->importancePack);
    }

    #[Override]
    public function recordNotification(): bool
    {
        return $this->seenPack->recordNotification();
    }

    #[Override]
    public function showSightingStatus(): string
    {
        return $this->seenPack->getStatusForCurrentUser();
    }

    #[Override]
    public function showSightingCSS(): string
    {
        return $this->seenPack->getCSSForCurrentUser();
    }

    public function getImportanceCategory(): string
    {
        return $this->getImportanceCategoryObject()->getName();
    }

    public function getImportanceCategoryObject(): ImportanceCategory
    {
        return ImportanceCategory::from($this->importance_category);
    }

    public function getImportanceCategoryCode(): string
    {
        return $this->importance_category;
    }

    public function getImportanceCategoryLowercase(): string
    {
        $importance = ImportanceCategory::from($this->importance_category);
        return $importance->getNameLowercase();
    }

    public function getLastModified(): DateTimeImmutable
    {
        return new DateTimeImmutable(date("Y-m-d H:i:s", $this->modified_at));
    }

    public function getSeenStatusForUser(int $userId): string
    {
        /** @var Seen $sighting */
        $sighting = $this->seenPack->getSightingsForUser($userId)->one();

        if (!$sighting) {
            return 'none';
        }

        return $sighting->status;
    }

    public function __toString()
    {
        return Html::a($this->name, ['location/view', 'key' => $this->key]);
    }
}
