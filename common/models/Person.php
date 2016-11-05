<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasDescriptions;
use common\models\core\HasEpicControl;
use common\models\core\HasVisibility;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "person".
 *
 * @property string $person_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $tagline
 * @property string $data
 * @property string $visibility
 * @property string $character_id
 * @property string $description_pack_id
 *
 * @property Epic $epic
 * @property Character $character
 * @property DescriptionPack $descriptionPack
 */
class Person extends ActiveRecord implements Displayable, HasDescriptions, HasEpicControl, HasVisibility
{
    use ToolsForEntity;

    const VISIBILITY_NONE = 'none';
    const VISIBILITY_LOGGED = 'logged';
    const VISIBILITY_GM = 'gm';
    const VISIBILITY_FULL = 'full';

    public static function tableName()
    {
        return 'person';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name', 'tagline', 'visibility'], 'required'],
            [['epic_id', 'character_id', 'description_pack_id'], 'integer'],
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
                ['character_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Character::className(),
                'targetAttribute' => ['character_id' => 'character_id']
            ],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::className(),
                'targetAttribute' => ['description_pack_id' => 'description_pack_id']
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
            'person_id' => Yii::t('app', 'PERSON_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'PERSON_KEY'),
            'name' => Yii::t('app', 'PERSON_NAME'),
            'tagline' => Yii::t('app', 'PERSON_TAGLINE'),
            'data' => Yii::t('app', 'PERSON_DATA'),
            'visibility' => Yii::t('app', 'PERSON_VISIBILITY'),
            'character_id' => Yii::t('app', 'LABEL_CHARACTER'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
            $this->data = json_encode([]);
        }

        if (empty($this->description_pack_id)) {
            $pack = DescriptionPack::create('Person');
            $this->description_pack_id = $pack->description_pack_id;
        }

        return parent::beforeSave($insert);
    }

    public function behaviors()
    {
        return [
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::className(),
                'idName' => 'person_id',
                'className' => 'Person',
            ]
        ];
    }

    /**
     * @return string[]
     */
    static public function visibilityNames():array
    {
        return [
            self::VISIBILITY_NONE => Yii::t('app', 'PERSON_VISIBILITY_NONE'),
            self::VISIBILITY_LOGGED => Yii::t('app', 'PERSON_VISIBILITY_LOGGED'),
            self::VISIBILITY_GM => Yii::t('app', 'PERSON_VISIBILITY_GM'),
            self::VISIBILITY_FULL => Yii::t('app', 'PERSON_VISIBILITY_FULL'),
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
        return $this->hasOne(Character::className(), ['character_id' => 'character_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDescriptionPack():ActiveQuery
    {
        return $this->hasOne(DescriptionPack::className(), ['description_pack_id' => 'description_pack_id']);
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
     * @param Character $character
     * @return null|Person
     */
    static public function createForCharacter(Character $character)
    {
        $person = new Person();
        $person->epic_id = $character->epic_id;
        $person->name = $character->name;
        $person->character_id = $character->character_id;
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

    static public function canUserIndexThem()
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic'], Yii::t('app', 'NO_RIGHTS_TO_LIST_PERSON'));
    }

    static public function canUserCreateThem()
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic'], Yii::t('app', 'NO_RIGHTS_TO_CREATE_PERSON'));
    }

    public function canUserControlYou()
    {
        return self::canUserControlInEpic($this->epic, Yii::t('app', 'NO_RIGHT_TO_CONTROL_PERSON'));
    }

    public function canUserViewYou()
    {
        return self::canUserViewInEpic($this->epic, Yii::t('app', 'NO_RIGHT_TO_VIEW_PERSON'));
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
}
