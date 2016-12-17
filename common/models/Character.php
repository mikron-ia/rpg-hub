<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasDescriptions;
use common\models\core\HasEpicControl;
use common\models\core\HasSightings;
use common\models\core\HasVisibility;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use Yii;
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
 * @property string $character_sheet_id
 * @property string $description_pack_id
 * @property string $external_data_pack_id
 * @property string $seen_pack_id
 *
 * @property Epic $epic
 * @property CharacterSheet $character
 * @property DescriptionPack $descriptionPack
 * @property ExternalDataPack $externalDataPack
 * @property SeenPack $seenPack
 * @property CharacterSheet[] $characterSheets
 */
class Character extends ActiveRecord implements Displayable, HasDescriptions, HasEpicControl, HasVisibility, HasSightings
{
    use ToolsForEntity;

    const VISIBILITY_NONE = 'none';
    const VISIBILITY_LOGGED = 'logged';
    const VISIBILITY_GM = 'gm';
    const VISIBILITY_FULL = 'full';

    public static function tableName()
    {
        return 'character';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name', 'tagline', 'visibility'], 'required'],
            [['epic_id', 'character_sheet_id', 'description_pack_id'], 'integer'],
            [['data', 'visibility'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name', 'tagline'], 'string', 'max' => 120],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['character_sheet_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => CharacterSheet::className(),
                'targetAttribute' => ['character_sheet_id' => 'character_sheet_id']
            ],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::className(),
                'targetAttribute' => ['description_pack_id' => 'description_pack_id']
            ],
            [
                ['external_data_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ExternalDataPack::className(),
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
            'character_sheet_id' => Yii::t('app', 'LABEL_CHARACTER'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
            'external_data_pack_id' => Yii::t('app', 'EXTERNAL_DATA_PACK'),
            'seen_pack_id' => Yii::t('app', 'SEEN_PACK_ID'),
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

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->seenPack->updateRecord();
        parent::afterSave($insert, $changedAttributes);
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
     * @return string[]
     */
    static public function visibilityNames():array
    {
        return [
            self::VISIBILITY_NONE => Yii::t('app', 'CHARACTER_VISIBILITY_NONE'),
            self::VISIBILITY_LOGGED => Yii::t('app', 'CHARACTER_VISIBILITY_LOGGED'),
            self::VISIBILITY_GM => Yii::t('app', 'CHARACTER_VISIBILITY_GM'),
            self::VISIBILITY_FULL => Yii::t('app', 'CHARACTER_VISIBILITY_FULL'),
        ];
    }

    /**
     * @return string[]
     */
    public function allowedVisibilities():array
    {
        return array_keys(self::visibilityNames());
    }

    /**
     * @return ActiveQuery
     */
    public function getEpic():ActiveQuery
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacter():ActiveQuery
    {
        return $this->hasOne(CharacterSheet::className(), ['character_sheet_id' => 'character_sheet_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDescriptionPack():ActiveQuery
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
    public function getSeenPack()
    {
        return $this->hasOne(SeenPack::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacterSheets()
    {
        return $this->hasMany(CharacterSheet::className(), ['currently_delivered_character_id' => 'character_id']);
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
        return ($this->visibility === self::VISIBILITY_FULL);
    }

    /**
     * @return string|null
     */
    public function getVisibilityName()
    {
        $list = self::visibilityNames();
        if (isset($list[$this->visibility])) {
            return $list[$this->visibility];
        } else {
            return null;
        }
    }

    /**
     * Creates person record for character
     * @param CharacterSheet $character
     * @return null|Character
     */
    static public function createForCharacter(CharacterSheet $character)
    {
        $person = new Character();
        $person->epic_id = $character->epic_id;
        $person->name = $character->name;
        $person->character_sheet_id = $character->character_sheet_id;
        $person->tagline = '?';
        $person->visibility = Visibility::VISIBILITY_GM;

        if ($person->save()) {
            $person->refresh();
            return $person;
        } else {
            return null;
        }
    }

    /**
     * Provides list of types allowed by this class
     * @return string[]
     */
    static public function allowedDescriptionTypes():array
    {
        return [
            Description::TYPE_HISTORY,
            Description::TYPE_APPEARANCE,
            Description::TYPE_PERSONALITY,
            Description::TYPE_WHO,
        ];
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

    public function getVisibility():string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getName();
    }

    public function getVisibilityLowercase():string
    {
        $visibility = Visibility::create($this->visibility);
        return $visibility->getNameLowercase();
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
