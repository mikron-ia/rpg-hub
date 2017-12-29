<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasDescriptions;
use common\models\core\HasEpicControl;
use common\models\core\HasImportance;
use common\models\core\HasImportanceCategory;
use common\models\core\HasSightings;
use common\models\core\HasVisibility;
use common\models\core\ImportanceCategory;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use Yii;
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
 * @property string $description_pack_id
 * @property string $external_data_pack_id
 * @property string $importance_pack_id
 * @property string $master_group_id
 *
 * @property DescriptionPack $descriptionPack
 * @property ExternalDataPack $externalDataPack
 * @property ImportancePack $importancePack
 * @property Epic $epic
 * @property Group $masterGroup
 * @property Group[] $subGroups
 * @property SeenPack $seenPack
 * @property GroupMembership[] $groupCharacterMemberships
 * @property GroupMembership[] $groupCharacterMembershipsOrderedByPosition
 * @property GroupMembership[] $groupCharacterMembershipsActive
 * @property GroupMembership[] $groupCharacterMembershipsPassive
 * @property GroupMembership[] $groupCharacterMembershipsPast
 */
class Group extends ActiveRecord implements Displayable, HasDescriptions, HasEpicControl, HasImportance, HasImportanceCategory, HasSightings, HasVisibility
{
    use ToolsForEntity;

    public static function tableName()
    {
        return 'group';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name'], 'required'],
            [['epic_id', 'master_group_id'], 'integer'],
            [['name'], 'string', 'max' => 120],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['visibility'],
                'in',
                'range' => function () {
                    return $this->allowedVisibilities();
                }
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

    public function attributeLabels()
    {
        return [
            'group_id' => Yii::t('app', 'GROUP_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'GROUP_KEY'),
            'name' => Yii::t('app', 'GROUP_NAME'),
            'data' => Yii::t('app', 'GROUP_DATA'),
            'visibility' => Yii::t('app', 'GROUP_VISIBILITY'),
            'importance_category' => Yii::t('app', 'GROUP_IMPORTANCE'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
            'external_data_pack_id' => Yii::t('app', 'EXTERNAL_DATA_PACK'),
            'importance_pack_id' => Yii::t('app', 'IMPORTANCE_PACK'),
            'master_group_id' => Yii::t('app', 'GROUP_MASTER_GROUP'),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->seenPack->updateRecord();
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
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

        if (empty($this->importance_pack_id)) {
            $pack = ImportancePack::create('Group');
            $this->importance_pack_id = $pack->importance_pack_id;
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

    public function behaviors()
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::className(),
                'idName' => 'group_id',
                'className' => 'Group',
            ]
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDescriptionPack()
    {
        return $this->hasOne(DescriptionPack::className(), ['description_pack_id' => 'description_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getExternalDataPack()
    {
        return $this->hasOne(ExternalDataPack::className(), ['external_data_pack_id' => 'external_data_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getImportancePack()
    {
        return $this->hasOne(ImportancePack::className(), ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasterGroup()
    {
        return $this->hasOne(Group::className(), ['group_id' => 'master_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubGroups()
    {
        return $this->hasMany(Group::className(), ['master_group_id' => 'group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSeenPack()
    {
        return $this->hasOne(SeenPack::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroupCharacterMemberships()
    {
        return $this->hasMany(GroupMembership::className(), ['group_id' => 'group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroupCharacterMembershipsOrderedByPosition()
    {
        return $this->hasMany(GroupMembership::className(), ['group_id' => 'group_id'])->orderBy('position ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getGroupCharacterMembershipsActive()
    {
        return $this->hasMany(GroupMembership::className(), ['group_id' => 'group_id'])->where([
            'status' => GroupMembership::STATUS_ACTIVE,
            'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic'])
        ])->orderBy('position ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getGroupCharacterMembershipsPast()
    {
        return $this->hasMany(GroupMembership::className(), ['group_id' => 'group_id'])->where([
            'status' => GroupMembership::STATUS_PAST,
            'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic'])
        ])->orderBy('position ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getGroupCharacterMembershipsPassive()
    {
        return $this->hasMany(GroupMembership::className(), ['group_id' => 'group_id'])->where([
            'status' => GroupMembership::STATUS_PASSIVE,
            'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic'])
        ])->orderBy('position ASC');
    }

    public function getSimpleDataForApi()
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

    public function getDescriptionPackId(): int
    {
        return $this->description_pack_id;
    }

    public function isVisibleInApi()
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
        $importance = ImportanceCategory::create($this->importance_category);
        return $importance->getName();
    }

    public function getImportanceCategoryCode(): string
    {
        return $this->importance_category;
    }

    public function getImportanceCategoryLowercase(): string
    {
        $importance = ImportanceCategory::create($this->importance_category);
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

    public function getLastModified(): \DateTimeImmutable
    {
        /* @todo Implement update date on object */
        return new \DateTimeImmutable('now');
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
