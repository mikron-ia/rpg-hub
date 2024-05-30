<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasDescriptions;
use common\models\core\HasEpicControl;
use common\models\core\HasImportance;
use common\models\core\HasImportanceCategory;
use common\models\core\HasScribbles;
use common\models\core\HasSightings;
use common\models\core\HasVisibility;
use common\models\core\ImportanceCategory;
use common\models\core\Visibility;
use common\models\external\HasReputations;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasDescriptions;
use DateTimeImmutable;
use ReflectionClass;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "group".
 *
 * @property string $group_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $seen_pack_id
 * @property string $visibility
 * @property string $importance_category
 * @property string $updated_at
 * @property int|null $description_pack_id
 * @property int|null $external_data_pack_id
 * @property int|null $importance_pack_id
 * @property int|null $master_group_id
 * @property int|null $scribble_pack_id
 * @property int|null $utility_bag_id
 *
 * @property DescriptionPack $descriptionPack
 * @property ExternalDataPack $externalDataPack
 * @property ImportancePack $importancePack
 * @property Epic $epic
 * @property Group $masterGroup
 * @property Group[] $subGroups
 * @property SeenPack $seenPack
 * @property ScribblePack $scribblePack
 * @property UtilityBag $utilityBag
 * @property GroupMembership[] $groupCharacterMemberships
 * @property GroupMembership[] $groupCharacterMembershipsOrderedByPosition
 * @property GroupMembership[] $groupCharacterMembershipsActive
 * @property GroupMembership[] $groupCharacterMembershipsPassive
 * @property GroupMembership[] $groupCharacterMembershipsPast
 */
class Group extends ActiveRecord implements Displayable, HasDescriptions, HasEpicControl, HasImportance, HasImportanceCategory, HasReputations, HasScribbles, HasSightings, HasVisibility
{
    use ToolsForEntity;
    use ToolsForHasDescriptions;

    public bool $is_off_the_record_change = false;

    public static function tableName(): string
    {
        return 'group';
    }

    public function rules(): array
    {
        return [
            [['epic_id', 'name'], 'required'],
            [
                [
                    'epic_id',
                    'seen_pack_id',
                    'updated_at',
                    'description_pack_id',
                    'external_data_pack_id',
                    'importance_pack_id',
                    'scribble_pack_id',
                    'utility_bag_id',
                    'master_group_id'
                ],
                'integer'
            ],
            [['name'], 'string', 'max' => 120],
            [['visibility', 'importance_category'], 'string', 'max' => 20],
            [['is_off_the_record_change'], 'boolean'],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['visibility'],
                'in',
                'range' => function () {
                    return $this->allowedVisibilities();
                }
            ],
            [
                ['importance_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ImportancePack::class,
                'targetAttribute' => ['importance_pack_id' => 'importance_pack_id']
            ],
            [
                ['master_group_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Group::class,
                'targetAttribute' => ['master_group_id' => 'group_id']
            ],
            [
                ['scribble_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ScribblePack::class,
                'targetAttribute' => ['scribble_pack_id' => 'scribble_pack_id']
            ],
            [
                ['seen_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SeenPack::class,
                'targetAttribute' => ['seen_pack_id' => 'seen_pack_id']
            ],
            [
                ['utility_bag_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => UtilityBag::class,
                'targetAttribute' => ['utility_bag_id' => 'utility_bag_id']
            ],
        ];
    }

    public function afterFind()
    {
        if ($this->seen_pack_id) {
            $this->seenPack->recordNotification();
        }
        parent::afterFind();
    }

    public function attributeLabels(): array
    {
        return [
            'group_id' => Yii::t('app', 'GROUP_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'GROUP_KEY'),
            'name' => Yii::t('app', 'GROUP_NAME'),
            'data' => Yii::t('app', 'GROUP_DATA'),
            'visibility' => Yii::t('app', 'GROUP_VISIBILITY'),
            'importance_category' => Yii::t('app', 'GROUP_IMPORTANCE'),
            'updated_at' => Yii::t('app', 'GROUP_UPDATED_AT'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
            'external_data_pack_id' => Yii::t('app', 'EXTERNAL_DATA_PACK'),
            'importance_pack_id' => Yii::t('app', 'IMPORTANCE_PACK'),
            'master_group_id' => Yii::t('app', 'GROUP_MASTER_GROUP'),
            'scribble_pack_id' => Yii::t('app', 'SCRIBBLE_PACK'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
            'is_off_the_record_change' => Yii::t('app', 'CHECK_OFF_THE_RECORD_CHANGE'),
        ];
    }

    public function afterSave($insert, $changedAttributes): void
    {
        if (!$this->is_off_the_record_change) {
            $this->seenPack->updateRecord();
            $this->utilityBag->flagAsChanged();
        }
        $this->utilityBag->flagForImportanceRecalculation();
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new ReflectionClass($this))->getShortName()));
            $this->data = json_encode([]);
        }

        if (empty($this->description_pack_id)) {
            $pack = DescriptionPack::create('Group');
            $this->description_pack_id = $pack->description_pack_id;
        }

        if (empty($this->external_data_pack_id)) {
            $pack = ExternalDataPack::create('Group');
            $this->external_data_pack_id = $pack->external_data_pack_id;
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('Group');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('Group');
            $this->utility_bag_id = $pack->utility_bag_id;
        }

        if (empty($this->importance_pack_id)) {
            $pack = ImportancePack::create('Group');
            $this->importance_pack_id = $pack->importance_pack_id;
        }

        if (empty($this->scribble_pack_id)) {
            $pack = ScribblePack::create('Group');
            $this->scribble_pack_id = $pack->scribble_pack_id;
        }

        return parent::beforeSave($insert);
    }

    static public function allowedDescriptionTypes(): array
    {
        return [
            Description::TYPE_WHO,
            Description::TYPE_HISTORY,
            Description::TYPE_ASPECTS,
            Description::TYPE_ATTITUDE,
            Description::TYPE_BACKGROUND,
            Description::TYPE_DOMAIN,
            Description::TYPE_FAME,
            Description::TYPE_FACTIONS,
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

    public function behaviors(): array
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'group_id',
                'className' => 'Group',
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => null,
            ],
        ];
    }

    public function getDescriptionPack(): ActiveQuery
    {
        return $this->hasOne(DescriptionPack::class, ['description_pack_id' => 'description_pack_id']);
    }

    public function getExternalDataPack(): ActiveQuery
    {
        return $this->hasOne(ExternalDataPack::class, ['external_data_pack_id' => 'external_data_pack_id']);
    }

    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    public function getImportancePack(): ActiveQuery
    {
        return $this->hasOne(ImportancePack::class, ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * Gets query for [[ScribblePack]]
     */
    public function getScribblePack(): ActiveQuery|ScribblePackQuery
    {
        return $this->hasOne(ScribblePack::class, ['scribble_pack_id' => 'scribble_pack_id']);
    }

    public function getMasterGroup(): ActiveQuery
    {
        return $this->hasOne(Group::class, ['group_id' => 'master_group_id']);
    }

    public function getSubGroups(): ActiveQuery
    {
        return $this->hasMany(Group::class, ['master_group_id' => 'group_id']);
    }

    public function getSeenPack(): ActiveQuery
    {
        return $this->hasOne(SeenPack::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getUtilityBag(): ActiveQuery
    {
        return $this->hasOne(UtilityBag::class, ['utility_bag_id' => 'utility_bag_id']);
    }

    public function getGroupCharacterMemberships(): ActiveQuery
    {
        return $this->hasMany(GroupMembership::class, ['group_id' => 'group_id']);
    }

    public function getGroupCharacterMembershipsOrderedByPosition(): ActiveQuery
    {
        return $this->hasMany(GroupMembership::class, ['group_id' => 'group_id'])->orderBy('position ASC');
    }

    public function getGroupCharacterMembershipsActive(): ActiveQuery
    {
        return $this->hasMany(GroupMembership::class, ['group_id' => 'group_id'])->where([
            'status' => GroupMembership::STATUS_ACTIVE,
            'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic'])
        ])->orderBy('position ASC');
    }

    public function getGroupCharacterMembershipsPast(): ActiveQuery
    {
        return $this->hasMany(GroupMembership::class, ['group_id' => 'group_id'])->where([
            'status' => GroupMembership::STATUS_PAST,
            'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic'])
        ])->orderBy('position ASC');
    }

    public function getGroupCharacterMembershipsPassive(): ActiveQuery
    {
        return $this->hasMany(GroupMembership::class, ['group_id' => 'group_id'])->where([
            'status' => GroupMembership::STATUS_PASSIVE,
            'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic'])
        ])->orderBy('position ASC');
    }

    public function getSimpleDataForApi(): array
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
        ];
    }

    public function getCompleteDataForApi()
    {
        $decodedData = json_decode($this->data, true);

        $decodedData['name'] = $this->name;
        $decodedData['key'] = $this->key;

        return $decodedData;
    }

    public function isVisibleInApi(): bool
    {
        return true;
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

    static function throwExceptionAboutCreate()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_GROUP'));
    }

    static function throwExceptionAboutControl()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_GROUP'));
    }

    static function throwExceptionAboutIndex()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_GROUP'));
    }

    static function throwExceptionAboutView()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_GROUP'));
    }

    public function recordSighting(): bool
    {
        return $this->seenPack->recordSighting();
    }

    public function recordNotification(): bool
    {
        return $this->seenPack->recordNotification();
    }

    public function showSightingStatus(): string
    {
        return $this->seenPack->getStatusForCurrentUser();
    }

    public function showSightingCSS(): string
    {
        return $this->seenPack->getCSSForCurrentUser();
    }

    static public function allowedVisibilities(): array
    {
        return [
            Visibility::VISIBILITY_GM,
            Visibility::VISIBILITY_FULL
        ];
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

    public function getVisibility(): string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getName();
    }

    public function getVisibilityLowercase(): string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getNameLowercase();
    }

    public function getLastModified(): DateTimeImmutable
    {
        return new DateTimeImmutable(date("Y-m-d H:i:s", $this->updated_at));
    }

    public function getSeenStatusForUser(int $userId): string
    {
        /** @var Seen $sighting */
        $sighting = $this->seenPack->getSightingsForUser($userId)->one();

        if (!$sighting) {
            return 'none';
        } else {
            return $sighting->status;
        }
    }

    public function __toString()
    {
        return Html::a($this->name, ['group/view', 'key' => $this->key]);
    }
}
