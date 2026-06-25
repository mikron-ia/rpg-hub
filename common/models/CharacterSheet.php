<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\CharacterSheetDataState;
use common\models\core\Displayable;
use common\models\core\HasEpicControl;
use common\models\core\HasKey;
use common\models\core\HasSightings;
use common\models\external\Tab;
use common\models\tools\ToolsForEntity;
use common\models\tools\ToolsForLinkTags;
use Override;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\HttpException;

/**
 * @property string $character_sheet_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $data_state
 * @property string|null $notes
 * @property string|null $notes_expanded
 * @property string $currently_delivered_character_id
 * @property string $player_id
 * @property string $seen_pack_id
 * @property string $utility_bag_id
 *
 * @property Epic $epic
 * @property Character $currentlyDeliveredCharacter
 * @property Character[] $characters
 * @property CharacterSheetDataState $dataState
 * @property User $player
 * @property SeenPack $seenPack
 * @property UtilityBag $utilityBag
 */
class CharacterSheet extends ActiveRecord implements Displayable, HasEpicControl, HasSightings, HasKey
{
    use ToolsForEntity;
    use ToolsForLinkTags;

    #[Override]
    public static function tableName(): string
    {
        return 'character_sheet';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'characterSheet';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['epic_id', 'name'], 'required'],
            [['epic_id', 'currently_delivered_character_id', 'player_id'], 'integer'],
            [['name'], 'string', 'max' => 120],
            [['data_state'], 'string', 'max' => 10],
            [['notes'], 'string'],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id'],
            ],
            [
                ['currently_delivered_character_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Character::class,
                'targetAttribute' => ['currently_delivered_character_id' => 'character_id'],
            ],
            [
                ['currently_delivered_character_id'],
                'in',
                'skipOnError' => true,
                'range' => $this->getPeopleAvailableToThisCharacterAsIdList(),
                'message' => Yii::t('app', 'CHARACTER_SHEET_ERROR_CHARACTER_NOT_CONNECTED'),
            ],
            [
                ['player_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['player_id' => 'id'],
            ],
            [
                ['data_state'],
                'in',
                'range' => $this->getDataState()->allowedSuccessorsAsKeys(),
                'message' => Yii::t(
                    'app',
                    'CHARACTER_SHEET_STATE_NOT_ALLOWED {allowed}',
                    ['allowed' => implode(', ', $this->getDataState()->allowedSuccessorsAsStrings())],
                ),
            ],
        ];
    }

    /**
     * @return array<string,string>
     */
    #[Override]
    public function attributeLabels(): array
    {
        return [
            'character_sheet_id' => Yii::t('app', 'CHARACTER_SHEET_ID'),
            'epic_id' => Yii::t('app', 'EPIC_LABEL'),
            'key' => Yii::t('app', 'CHARACTER_SHEET_KEY'),
            'name' => Yii::t('app', 'CHARACTER_SHEET_NAME'),
            'data' => Yii::t('app', 'CHARACTER_SHEET_DATA'),
            'data_state' => Yii::t('app', 'CHARACTER_SHEET_DATA_STATE'),
            'notes' => Yii::t('app', 'CHARACTER_SHEET_NOTES'),
            'notes_expanded' => Yii::t('app', 'CHARACTER_SHEET_NOTES'),
            'currently_delivered_character_id' => Yii::t('app', 'CHARACTER_SHEET_DELIVERED_CHARACTER_ID'),
            'player_id' => Yii::t('app', 'CHARACTER_SHEET_PLAYER'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
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

    /**
     * @throws Exception
     */
    #[Override]
    public function afterSave($insert, $changedAttributes): void
    {
        $this->seenPack->updateRecord();
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
            $this->data = json_encode([]);
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('CharacterSheet');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('CharacterSheet');
            $this->utility_bag_id = $pack->utility_bag_id;
        }

        $this->notes_expanded = $this->expandText($this->notes);

        return parent::beforeSave($insert);
    }

    /**
     * @return array<string,array<string,string>>
     */
    #[Override]
    public function behaviors(): array
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::class,
                'idName' => 'character_sheet_id',
                'className' => 'CharacterSheet',
            ]
        ];
    }

    #[Override]
    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    public function getCurrentlyDeliveredCharacter(): ActiveQuery
    {
        return $this->hasOne(Character::class, ['character_id' => 'currently_delivered_character_id']);
    }

    public function getCharacters(): ActiveQuery
    {
        return $this->hasMany(Character::class, ['character_sheet_id' => 'character_sheet_id']);
    }

    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'player_id']);
    }

    public function getSeenPack(): ActiveQuery
    {
        return $this->hasOne(SeenPack::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getUtilityBag(): ActiveQuery
    {
        return $this->hasOne(UtilityBag::class, ['utility_bag_id' => 'utility_bag_id']);
    }

    public function getDataState(): CharacterSheetDataState
    {
        return CharacterSheetDataState::from($this->data_state);
    }

    public function getNotesFormatted(): string
    {
        return $this->formatText($this->notes_expanded ?? $this->notes, false);
    }

    /**
     * @return array<string,string>
     */
    #[Override]
    public function getSimpleDataForApi(): array
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
        ];
    }

    #[Override]
    public function getCompleteDataForApi(): array
    {
        $decodedData = json_decode($this->data, true);

        $decodedData['name'] = $this->name;
        $decodedData['key'] = $this->key;

        if (isset($this->currently_delivered_character_id)) {
            $decodedData['person'] = $this->currentlyDeliveredCharacter->getCompleteDataForApi();
        }

        return $decodedData;
    }

    #[Override]
    public function isVisibleInApi(): bool
    {
        return true;
    }

    /**
     * @return array<string,string>
     */
    public function getPeopleAvailableToThisCharacterAsDropDownList(): array
    {
        $query = new ActiveDataProvider([
            'query' => $this->getCharacters()
        ]);

        /* @var $peopleList Character[] */
        $peopleList = $query->getModels();
        $dropDownList = [];

        foreach ($peopleList as $person) {
            $dropDownList[$person->character_id] = $person->name;
        }

        return $dropDownList;
    }

    /**
     * Creates character sheet record for character
     *
     * @throws Exception
     */
    public static function createForCharacter(Character $character): ?CharacterSheet
    {
        $characterSheet = new CharacterSheet();
        $characterSheet->epic_id = $character->epic_id;
        $characterSheet->name = $character->name;
        $characterSheet->currently_delivered_character_id = $character->character_id;
        $characterSheet->data_state = CharacterSheetDataState::Incomplete->value;

        // validation is disabled because data is internal and ID assignment is mutual
        if ($characterSheet->save(false)) {
            $characterSheet->refresh();
            return $characterSheet;
        }

        return null;
    }

    /**
     * @return array<int,int>
     */
    public function getPeopleAvailableToThisCharacterAsIdList(): array
    {
        return array_keys($this->getPeopleAvailableToThisCharacterAsDropDownList());
    }

    public static function canUserIndexThem(): bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    public static function canUserCreateThem(): bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    public function canUserControlYou(): bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    public function canUserViewYou(): bool
    {
        return self::canUserViewInEpic($this->epic)
            && (
                Participant::participantHasRole(
                    Yii::$app->user->identity,
                    Yii::$app->params['activeEpic'],
                    ParticipantRole::ROLE_GM
                ) || $this->player_id === Yii::$app->user->id
            );
    }

    public static function throwExceptionAboutCreate(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_CHARACTER'));
    }

    public static function throwExceptionAboutControl(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_CHARACTER'));
    }

    public static function throwExceptionAboutIndex(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_CHARACTER'));
    }

    public static function throwExceptionAboutView(): void
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_CHARACTER'));
    }

    #[Override]
    public function recordSighting(): bool
    {
        return $this->seenPack->recordSighting();
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

    /**
     * Loads and saves external data
     *
     * @throws Exception
     */
    public function loadExternal(string $data): bool
    {
        if (empty($data)) {
            return false;
        }

        /* Try for JSON */
        $array = json_decode($data);

        if (!$array) {
            /* Try for encoded JSON */
            $decodedData = base64_decode($data);
            $array = json_decode($decodedData);
        }

        if (!$array) {
            /* If invalid JSON */
            return false;
        }

        /* If valid JSON */
        $this->data = json_encode($array);

        return $this->save();
    }

    /**
     * @return Tab[]
     */
    public function presentExternal(): array
    {
        $tabs = [];

        $data = json_decode($this->data, true);

        foreach ($data as $row) {
            if (is_array($row)) {
                $tabs[] = Tab::createFromData($row);
            }
        }

        return $tabs;
    }
}
