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
use common\models\external\HasReputations;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForHasDescriptions;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "person".
 *
 * @property string $character_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $tagline
 * @property string $data
 * @property string $visibility
 * @property string $importance_category
 * @property string $updated_at
 * @property string $character_sheet_id
 * @property string $description_pack_id
 * @property string $external_data_pack_id
 * @property string $seen_pack_id
 * @property string $importance_pack_id
 * @property string $utility_bag_id
 *
 * @property Epic $epic
 * @property CharacterSheet $characterSheet
 * @property DescriptionPack $descriptionPack
 * @property ExternalDataPack $externalDataPack
 * @property ImportancePack $importancePack
 * @property SeenPack $seenPack
 * @property UtilityBag $utilityBag
 * @property CharacterSheet[] $characterSheets
 * @property GroupMembership[] $groupMemberships
 * @property GroupMembership[] $groupMembershipsVisibleToUser
 */
class Character extends ActiveRecord implements Displayable, HasDescriptions, HasEpicControl, HasImportance, HasImportanceCategory, HasReputations, HasVisibility, HasSightings
{
    use ToolsForEntity;
    use ToolsForHasDescriptions;

    public static function tableName()
    {
        return 'character';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name', 'tagline', 'visibility', 'importance_category'], 'required'],
            [['epic_id', 'character_sheet_id', 'description_pack_id'], 'integer'],
            [['data', 'visibility', 'importance_category'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name', 'tagline'], 'string', 'max' => 120],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['character_sheet_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => CharacterSheet::class,
                'targetAttribute' => ['character_sheet_id' => 'character_sheet_id']
            ],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::class,
                'targetAttribute' => ['description_pack_id' => 'description_pack_id']
            ],
            [
                ['external_data_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ExternalDataPack::class,
                'targetAttribute' => ['external_data_pack_id' => 'external_data_pack_id']
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

    public function attributeLabels()
    {
        return [
            'character_id' => Yii::t('app', 'CHARACTER_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'CHARACTER_KEY'),
            'name' => Yii::t('app', 'CHARACTER_NAME'),
            'tagline' => Yii::t('app', 'CHARACTER_TAGLINE'),
            'data' => Yii::t('app', 'CHARACTER_DATA'),
            'visibility' => Yii::t('app', 'CHARACTER_VISIBILITY'),
            'importance_category' => Yii::t('app', 'CHARACTER_IMPORTANCE'),
            'updated_at' => Yii::t('app', 'CHARACTER_UPDATED_AT'),
            'character_sheet_id' => Yii::t('app', 'LABEL_CHARACTER'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
            'external_data_pack_id' => Yii::t('app', 'EXTERNAL_DATA_PACK'),
            'seen_pack_id' => Yii::t('app', 'SEEN_PACK_ID'),
            'importance_pack_id' => Yii::t('app', 'IMPORTANCE_PACK'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
        ];
    }

    public function afterFind()
    {
        if ($this->seen_pack_id) {
            $this->seenPack->recordNotification();
        }

        parent::afterFind();
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
            $this->data = json_encode([]);
        }

        if (empty($this->description_pack_id)) {
            $pack = DescriptionPack::create('Character');
            $this->description_pack_id = $pack->description_pack_id;
        }

        if (empty($this->external_data_pack_id)) {
            $pack = ExternalDataPack::create('Character');
            $this->external_data_pack_id = $pack->external_data_pack_id;
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('Character');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('Character');
            $this->utility_bag_id = $pack->utility_bag_id;
        }

        if (empty($this->importance_pack_id)) {
            $pack = ImportancePack::create('Character');
            $this->importance_pack_id = $pack->importance_pack_id;
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->seenPack->updateRecord();
        $this->utilityBag->flagAsChanged();
        $this->utilityBag->flagForImportanceRecalculation();
        parent::afterSave($insert, $changedAttributes);
    }

    public function behaviors()
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'character_id',
                'className' => 'Character',
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => null,
            ],
        ];
    }

    static public function allowedVisibilities(): array
    {
        return [
            Visibility::VISIBILITY_GM,
            Visibility::VISIBILITY_FULL
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacter(): ActiveQuery
    {
        return $this->hasOne(CharacterSheet::class, ['character_sheet_id' => 'character_sheet_id']);
    }

    public function getDescriptionPack(): ActiveQuery
    {
        return $this->hasOne(DescriptionPack::class, ['description_pack_id' => 'description_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getExternalDataPack()
    {
        return $this->hasOne(ExternalDataPack::class, ['external_data_pack_id' => 'external_data_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImportancePack()
    {
        return $this->hasOne(ImportancePack::class, ['importance_pack_id' => 'importance_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSeenPack()
    {
        return $this->hasOne(SeenPack::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUtilityBag()
    {
        return $this->hasOne(UtilityBag::class, ['utility_bag_id' => 'utility_bag_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacterSheets()
    {
        return $this->hasMany(CharacterSheet::class, ['currently_delivered_character_id' => 'character_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMemberships()
    {
        return $this->hasMany(GroupMembership::class, ['character_id' => 'character_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupMembershipsVisibleToUser()
    {
        return $this->hasMany(GroupMembership::class, ['character_id' => 'character_id'])->where([
            'visibility' => Visibility::determineVisibilityVector(Yii::$app->params['activeEpic'])
        ])->orderBy('status');
    }

    public function getSimpleDataForApi()
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
            'tagline' => $this->tagline,
            'tags' => [],
        ];
    }

    public function getCompleteDataForApi()
    {
        $decodedData = json_decode($this->data, true);

        $decodedData['name'] = $this->name;
        $decodedData['key'] = $this->key;
        $decodedData['tagline'] = $this->tagline;

        if ($this->description_pack_id) {
            $descriptions = $this->descriptionPack->getCompleteDataForApi();
            $decodedData['descriptions'] = [];
            foreach ($descriptions as $description) {
                $decodedData['descriptions'][] = $description;
            }
        }

        return $decodedData;
    }

    public function isVisibleInApi()
    {
        return ($this->visibility === Visibility::VISIBILITY_FULL);
    }

    /**
     * @return string|null
     */
    public function getVisibilityName()
    {
        $list = Visibility::visibilityNames(self::allowedVisibilities());
        if (isset($list[$this->visibility])) {
            return $list[$this->visibility];
        } else {
            return null;
        }
    }

    /**
     * Creates character record for character sheet
     * @param CharacterSheet $characterSheet
     * @return null|Character
     */
    static public function createForCharacterSheet(CharacterSheet $characterSheet)
    {
        $character = new Character();
        $character->epic_id = $characterSheet->epic_id;
        $character->name = $characterSheet->name;
        $character->character_sheet_id = $characterSheet->character_sheet_id;
        $character->tagline = '?';
        $character->visibility = Visibility::VISIBILITY_GM;
        $character->importance_category = ImportanceCategory::IMPORTANCE_MEDIUM;

        if ($character->save()) {
            $character->refresh();
            return $character;
        } else {
            return null;
        }
    }

    /**
     * Provides list of types allowed by this class
     * @return string[]
     */
    static public function allowedDescriptionTypes(): array
    {
        return [
            Description::TYPE_WHO,
            Description::TYPE_APPEARANCE,
            Description::TYPE_PERSONALITY,
            Description::TYPE_HISTORY,
            Description::TYPE_ASPECTS,
            Description::TYPE_ATTITUDE,
            Description::TYPE_BACKGROUND,
            Description::TYPE_DOMAIN,
            Description::TYPE_FAME,
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
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_CHARACTER'));
    }

    static function throwExceptionAboutControl()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_CHARACTER'));
    }

    static function throwExceptionAboutIndex()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_CHARACTER'));
    }

    static function throwExceptionAboutView()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_CHARACTER'));
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

    public function getLastModified(): \DateTimeImmutable
    {
        return new \DateTimeImmutable(date("Y-m-d H:i:s", $this->updated_at));
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
}
