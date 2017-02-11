<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasVisibility;
use common\models\core\Language;
use common\models\core\Visibility;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "description".
 *
 * @property string $description_id
 * @property string $description_pack_id
 * @property string $title
 * @property string $code
 * @property string $public_text
 * @property string $private_text
 * @property string $lang
 * @property string $visibility
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $created_by
 * @property string $updated_by
 *
 * @property User $createdBy
 * @property DescriptionPack $descriptionPack
 * @property User $updatedBy
 */
class Description extends ActiveRecord implements Displayable, HasVisibility
{
    const TYPE_APPEARANCE = 'appearance';       // The looks
    const TYPE_ASPECTS = 'aspects';             // Aspects - this is for FATE-like games
    const TYPE_ATTITUDE = 'attitude';           // Attitude towards different people / groups
    const TYPE_BACKGROUND = 'background';       // Origin, education, the like
    const TYPE_COMMENTARY = 'commentary';       // GM commentary
    const TYPE_DOMAIN = 'domain';               // Places where the person reigns, dominates, or frequents
    const TYPE_FAME = 'fame';                   // Famous deeds or events
    const TYPE_FACTIONS = 'factions';           // Factions associated with; this includes nations
    const TYPE_HISTORY = 'history';             // History of the person
    const TYPE_INTERACTIONS = 'interactions';   // Interactions / encounters with the group or person NAMES
    const TYPE_PERSONALITY = 'personality';     // Personality, character behaviour, mental issues
    const TYPE_RESOURCES = 'resources';         // Resources the person wields, flaunts, can offer
    const TYPE_REPUTATION = 'reputation';       // Character's reputation
    const TYPE_RETINUE = 'retinue';             // Friends, allies, etc.
    const TYPE_RUMOURS = 'rumours';             // Unproven rumours collected about character
    const TYPE_STORIES = 'stories';             // Stories person participated in
    const TYPE_THREADS = 'threads';             // Threads person was attached to
    const TYPE_WHO = 'who';                     // Who is this?

    public static function tableName()
    {
        return 'description';
    }

    public function rules()
    {
        return [
            [['description_pack_id', 'position'], 'integer'],
            [['description_pack_id', 'code', 'public_text', 'lang', 'visibility'], 'required'],
            [['public_text', 'private_text'], 'string'],
            [['code'], 'string', 'max' => 40],
            [['lang'], 'string', 'max' => 8],
            [['visibility'], 'string', 'max' => 20],
            [
                ['description_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DescriptionPack::className(),
                'targetAttribute' => ['description_pack_id' => 'description_pack_id']
            ],
            [
                ['code'],
                'in',
                'range' => function () {
                    return $this->allowedTypes();
                }
            ],
            [
                ['visibility'],
                'in',
                'range' => function () {
                    return Visibility::allowedVisibilities();
                }
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            $this->createHistoryRecord();
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!empty($changedAttributes)) {
            $this->descriptionPack->touch('updated_at');
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function attributeLabels()
    {
        return [
            'description_id' => Yii::t('app', 'DESCRIPTION_ID'),
            'description_pack_id' => Yii::t('app', 'DESCRIPTION_PACK'),
            'title' => Yii::t('app', 'DESCRIPTION_TITLE'),
            'code' => Yii::t('app', 'DESCRIPTION_CODE'),
            'public_text' => Yii::t('app', 'DESCRIPTION_TEXT_PUBLIC'),
            'private_text' => Yii::t('app', 'DESCRIPTION_TEXT_PRIVATE'),
            'lang' => Yii::t('app', 'LABEL_LANGUAGE'),
            'visibility' => Yii::t('app', 'LABEL_VISIBILITY'),
        ];
    }

    public function behaviors()
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => ['description_pack_id', 'lang'],
            ],
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::className(),
                'idName' => 'description_id',
                'className' => 'Description',
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
            ],
            'blameableBehavior' => [
                'class' => BlameableBehavior::className(),
            ],
        ];
    }

    /**
     * @return string[]
     */
    static public function typeNames():array
    {
        return [
            self::TYPE_APPEARANCE => Yii::t('app', 'DESCRIPTION_TYPE_APPEARANCE'),
            self::TYPE_ASPECTS => Yii::t('app', 'DESCRIPTION_TYPE_ASPECTS'),
            self::TYPE_ATTITUDE => Yii::t('app', 'DESCRIPTION_TYPE_ATTITUDE'),
            self::TYPE_BACKGROUND => Yii::t('app', 'DESCRIPTION_TYPE_BACKGROUND'),
            self::TYPE_COMMENTARY => Yii::t('app', 'DESCRIPTION_TYPE_COMMENTARY'),
            self::TYPE_DOMAIN => Yii::t('app', 'DESCRIPTION_TYPE_DOMAIN'),
            self::TYPE_FAME => Yii::t('app', 'DESCRIPTION_TYPE_FAME'),
            self::TYPE_FACTIONS => Yii::t('app', 'DESCRIPTION_TYPE_FACTIONS'),
            self::TYPE_INTERACTIONS => Yii::t('app', 'DESCRIPTION_TYPE_INTERACTIONS'),
            self::TYPE_HISTORY => Yii::t('app', 'DESCRIPTION_TYPE_HISTORY'),
            self::TYPE_PERSONALITY => Yii::t('app', 'DESCRIPTION_TYPE_PERSONALITY'),
            self::TYPE_RESOURCES => Yii::t('app', 'DESCRIPTION_TYPE_RESOURCES'),
            self::TYPE_REPUTATION => Yii::t('app', 'DESCRIPTION_TYPE_REPUTATION'),
            self::TYPE_RETINUE => Yii::t('app', 'DESCRIPTION_TYPE_RETINUE'),
            self::TYPE_RUMOURS => Yii::t('app', 'DESCRIPTION_TYPE_RUMOURS'),
            self::TYPE_STORIES => Yii::t('app', 'DESCRIPTION_TYPE_STORIES'),
            self::TYPE_THREADS => Yii::t('app', 'DESCRIPTION_TYPE_THREADS'),
            self::TYPE_WHO => Yii::t('app', 'DESCRIPTION_TYPE_WHO'),
        ];
    }

    /**
     * @return string[]
     */
    public function typeNamesForThisClass():array
    {
        $typeNamesAll = self::typeNames();
        $typeNamesAccepted = [];

        $class = 'common\models\\';

        if (method_exists($class, 'allowedTypes')) {
            $typesAllowed = call_user_func([$class . $this->descriptionPack->class, 'allowedTypes']);
        } else {
            $typesAllowed = array_keys($typeNamesAll);
        }

        foreach ($typeNamesAll as $typeKey => $typeName) {
            if (in_array($typeKey, $typesAllowed, true)) {
                $typeNamesAccepted[$typeKey] = $typeName;
            }
        }

        return $typeNamesAccepted;
    }

    static public function typesForCharacter():array
    {
        return [self::TYPE_PERSONALITY];
    }

    static public function typesForPerson():array
    {
        return [self::TYPE_APPEARANCE, self::TYPE_HISTORY, self::TYPE_WHO];
    }

    /**
     * @return string[]
     */
    public function allowedTypes():array
    {
        return array_keys(self::typeNames());
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy():ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
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
    public function getUpdatedBy():ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return string|null
     */
    public function getTypeName()
    {
        $names = self::typeNames();
        if (isset($names[$this->code])) {
            return $names[$this->code];
        } else {
            return "?";
        }
    }

    public function getLanguage()
    {
        $language = Language::create($this->lang);
        return $language->getName();
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

    /**
     * @return string|null
     */
    public function getPublicFormatted()
    {
        return Markdown::process(Html::encode($this->public_text), 'gfm');
    }

    /**
     * @return string|null
     */
    public function getPrivateFormatted()
    {
        return Markdown::process(Html::encode($this->private_text), 'gfm');
    }

    /**
     * Provides simple representation of the object content, fit for basic display in an index or a summary
     * @return array
     */
    public function getSimpleDataForApi()
    {
        return [
            'title' => $this->getTypeName(),
        ];
    }

    /**
     * Provides complete representation of public parts of object content, fit for full card display
     * @return array
     */
    public function getCompleteDataForApi()
    {
        return [
            'title' => $this->getTypeName(),
            'text' => $this->getPublicFormatted(),
        ];
    }

    public function isVisibleInApi()
    {
        return ($this->visibility === Visibility::VISIBILITY_FULL);
    }

    /**
     * @return DescriptionHistory|null
     */
    public function createHistoryRecord()
    {
        $description = Description::findOne(['description_id' => $this->description_id]);

        if (($description->public_text === $this->public_text) && ($description->private_text === $this->private_text)) {
            return null;
        }

        return DescriptionHistory::createFromDescription($description);
    }
}
