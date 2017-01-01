<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\core\HasSightings;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "character_sheet".
 *
 * @property string $character_sheet_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $currently_delivered_character_id
 * @property string $seen_pack_id
 *
 * @property Epic $epic
 * @property Character $currentlyDeliveredPerson
 * @property Character[] $people
 * @property SeenPack $seenPack
 */
class CharacterSheet extends ActiveRecord implements Displayable, HasEpicControl, HasSightings
{
    use ToolsForEntity;

    public static function tableName()
    {
        return 'character_sheet';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name'], 'required'],
            [['epic_id', 'currently_delivered_character_id'], 'integer'],
            [['name'], 'string', 'max' => 120],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['currently_delivered_character_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Character::className(),
                'targetAttribute' => [
                    'currently_delivered_character_id' => 'character_id'
                ]
            ],
            [
                ['currently_delivered_character_id'],
                'in',
                'skipOnError' => true,
                'range' => $this->getPeopleAvailableToThisCharacterAsIdList(),
                'message' => Yii::t('app', 'CHARACTER_SHEET_ERROR_CHARACTER_NOT_CONNECTED'),
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'character_sheet_id' => Yii::t('app', 'CHARACTER_SHEET_ID'),
            'epic_id' => Yii::t('app', 'EPIC_LABEL'),
            'key' => Yii::t('app', 'CHARACTER_SHEET_KEY'),
            'name' => Yii::t('app', 'CHARACTER_SHEET_NAME'),
            'data' => Yii::t('app', 'CHARACTER_SHEET_DATA'),
            'currently_delivered_character_id' => Yii::t('app', 'CHARACTER_SHEET_DELIVERED_CHARACTER_ID'),
        ];
    }

    public function afterFind()
    {
        if ($this->seen_pack_id) {
            $this->seenPack->recordNotification();
        }
        parent::afterFind();
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->seenPack->updateRecord();
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey('characterSheet');
            $this->data = json_encode([]);
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('CharacterSheet');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        return parent::beforeSave($insert);
    }

    public function behaviors()
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::className(),
                'idName' => 'character_sheet_id',
                'className' => 'CharacterSheet',
            ]
        ];
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
    public function getCurrentlyDeliveredPerson()
    {
        return $this->hasOne(Character::className(), ['character_id' => 'currently_delivered_character_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasMany(Character::className(), ['character_sheet_id' => 'character_sheet_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSeenPack():ActiveQuery
    {
        return $this->hasOne(SeenPack::className(), ['seen_pack_id' => 'seen_pack_id']);
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

        if (isset($this->currently_delivered_character_id)) {
            $decodedData['person'] = $this->currentlyDeliveredPerson->getCompleteDataForApi();
        }

        return $decodedData;
    }

    public function isVisibleInApi()
    {
        return true;
    }

    public function getPeopleAvailableToThisCharacterAsDropDownList()
    {
        $query = new ActiveDataProvider([
            'query' => $this->getPeople()
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
     * @param Character $character
     * @return null|CharacterSheet
     */
    static public function createForCharacter(Character $character)
    {
        $characterSheet = new CharacterSheet();
        $characterSheet->epic_id = $character->epic_id;
        $characterSheet->name = $character->name;

        if ($characterSheet->save()) {
            $characterSheet->refresh();
            return $characterSheet;
        } else {
            return null;
        }
    }

    /**
     * @return int[]
     */
    public function getPeopleAvailableToThisCharacterAsIdList():array
    {
        return array_keys($this->getPeopleAvailableToThisCharacterAsDropDownList());
    }

    static public function canUserIndexThem():bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    static public function canUserCreateThem():bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    public function canUserControlYou():bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    /**
     * {@inheritDoc}
     * @todo Add control on player level for front-end use
     */
    public function canUserViewYou():bool
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

    public function recordSighting():bool
    {
        return $this->seenPack->recordSighting();
    }

    public function recordNotification():bool
    {
        return $this->seenPack->recordNotification();
    }

    public function showSightingStatus():string
    {
        return $this->seenPack->getStatusForCurrentUser();
    }

    public function showSightingCSS():string
    {
        return $this->seenPack->getCSSForCurrentUser();
    }
}
