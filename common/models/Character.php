<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "character".
 *
 * @property string $character_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $currently_delivered_person_id
 *
 * @property Epic $epic
 * @property Person $currentlyDeliveredPerson
 * @property Person[] $people
 */
class Character extends ActiveRecord implements Displayable, HasEpicControl
{
    use ToolsForEntity;

    public static function tableName()
    {
        return 'character';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name'], 'required'],
            [['epic_id', 'currently_delivered_person_id'], 'integer'],
            [['name'], 'string', 'max' => 120],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['currently_delivered_person_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Person::className(),
                'targetAttribute' => [
                    'currently_delivered_person_id' => 'person_id'
                ]
            ],
            [
                ['currently_delivered_person_id'],
                'in',
                'skipOnError' => true,
                'range' => $this->getPeopleAvailableToThisCharacterAsIdList(),
                'message' => Yii::t('app', 'CHARACTER_SHEET_ERROR_PERSON_NOT_CONNECTED'),
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'character_id' => Yii::t('app', 'CHARACTER_SHEET_ID'),
            'epic_id' => Yii::t('app', 'EPIC_LABEL'),
            'key' => Yii::t('app', 'CHARACTER_SHEET_KEY'),
            'name' => Yii::t('app', 'CHARACTER_SHEET_NAME'),
            'data' => Yii::t('app', 'CHARACTER_SHEET_DATA'),
            'currently_delivered_person_id' => Yii::t('app', 'CHARACTER_SHEET_DELIVERED_PERSON_ID'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
            $this->data = json_encode([]);

            /* Create and attach person */
            $person = Person::createForCharacter($this);
            if ($person) {
                $this->currently_delivered_person_id = $person->person_id;
            }
        }

        return parent::beforeSave($insert);
    }

    public function behaviors()
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::className(),
                'idName' => 'character_id',
                'className' => 'Character',
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
        return $this->hasOne(Person::className(), ['person_id' => 'currently_delivered_person_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasMany(Person::className(), ['character_id' => 'character_id']);
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

        if (isset($this->currently_delivered_person_id)) {
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

        /* @var $peopleList Person[] */
        $peopleList = $query->getModels();
        $dropDownList = [];

        foreach ($peopleList as $person) {
            $dropDownList[$person->person_id] = $person->name;
        }

        return $dropDownList;

    }

    public function getPeopleAvailableToThisCharacterAsIdList()
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
}
