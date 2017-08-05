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
use yii\helpers\StringHelper;
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
    const TYPE_APPEARANCE = 'appearance';       // For Character; The looks
    const TYPE_ASPECTS = 'aspects';             // For Character, Scenario, Story; Aspects - this is for FATE-like games
    const TYPE_ATTITUDE = 'attitude';           // For Character; Attitude towards different people / groups
    const TYPE_BACKGROUND = 'background';       // For Character, Story; Origin, education, the like
    const TYPE_COMMENTARY = 'commentary';       // For Character, Story; GM commentary
    const TYPE_DOMAIN = 'domain';               // For Character; Places where the person reigns, dominates, or frequents
    const TYPE_FAME = 'fame';                   // For Character; Famous deeds or events
    const TYPE_FACTIONS = 'factions';           // For Character; Factions associated with; this includes nations
    const TYPE_HISTORY = 'history';             // For Character; History of the person
    const TYPE_INTERACTIONS = 'interactions';   // For Character; Interactions / encounters with the group or person NAMES
    const TYPE_PERSONALITY = 'personality';     // For Character; Personality, character behaviour, mental issues
    const TYPE_RESOURCES = 'resources';         // For Character; Resources the person wields, flaunts, can offer
    const TYPE_REPUTATION = 'reputation';       // For Character; Character's reputation
    const TYPE_RETINUE = 'retinue';             // For Character; Friends, allies, etc.
    const TYPE_RUMOURS = 'rumours';             // For Character; Unproven rumours collected about character
    const TYPE_STORIES = 'stories';             // For Character; Stories person participated in
    const TYPE_THREADS = 'threads';             // For Character, Scenario, Story; Threads attached
    const TYPE_WHO = 'who';                     // For Character; Who is this?

    const TYPE_PREMISE = 'premise';             // For Scenario, Story; what is the main concept?
    const TYPE_ACTORS = 'actors';               // For Scenario, Story; who is going to participate?
    const TYPE_PLAN = 'plan';                   // For Scenario; what is going to happen?
    const TYPE_SCENE = 'scene';                 // For Scenario, Story; a particular scene
    const TYPE_ACT = 'act';                     // For Scenario, Story; a particular act
    const TYPE_BRIEFING = 'briefing';           // For Scenario, Story; briefing / introduction scene
    const TYPE_DEBRIEFING = 'debriefing';       // For Scenario, Story; debriefing / aftermath scene
    const TYPE_PRELUDE = 'prelude';             // For Scenario, Story; events leading to or introducing
    const TYPE_INTERLUDE = 'interlude';         // For Scenario, Story; events in-between
    const TYPE_POSTLUDE = 'postlude';           // For Scenario, Story; events following

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
                    return $this->allowedVisibilities();
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
                'groupAttributes' => ['description_pack_id'],
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
            self::TYPE_PREMISE => Yii::t('app', 'DESCRIPTION_TYPE_PREMISE'),
            self::TYPE_ACTORS => Yii::t('app', 'DESCRIPTION_TYPE_ACTORS'),
            self::TYPE_PLAN => Yii::t('app', 'DESCRIPTION_TYPE_PLAN'),
            self::TYPE_SCENE => Yii::t('app', 'DESCRIPTION_TYPE_SCENE'),
            self::TYPE_ACT => Yii::t('app', 'DESCRIPTION_TYPE_ACT'),
            self::TYPE_BRIEFING => Yii::t('app', 'DESCRIPTION_TYPE_BRIEFING'),
            self::TYPE_DEBRIEFING => Yii::t('app', 'DESCRIPTION_TYPE_DEBRIEFING'),
            self::TYPE_PRELUDE => Yii::t('app', 'DESCRIPTION_TYPE_PRELUDE'),
            self::TYPE_INTERLUDE => Yii::t('app', 'DESCRIPTION_TYPE_INTERLUDE'),
            self::TYPE_POSTLUDE => Yii::t('app', 'DESCRIPTION_TYPE_POSTLUDE'),
        ];
    }

    /**
     * Provides list of allowed description types for the class the object belongs to
     * List is provided as full names in current language keyed by codes
     *
     * Fallback mechanism: if the method is not implemented, a full list is provided, which will allow the object to be
     * used, but will reduce the user experience.
     *
     * @return string[]
     */
    public function typeNamesForThisClass():array
    {
        $typeNamesAll = self::typeNames();
        $typeNamesAccepted = [];

        $class = 'common\models\\' . $this->descriptionPack->class;

        if (method_exists($class, 'allowedDescriptionTypes')) {
            $typesAllowed = call_user_func([$class, 'allowedDescriptionTypes']);
        } else {
            $typesAllowed = array_keys($typeNamesAll);
        }

        foreach ($typesAllowed as $typeKey) {
            if (isset($typeNamesAll[$typeKey])) {
                $typeNamesAccepted[$typeKey] = $typeNamesAll[$typeKey];
            }
        }

        return $typeNamesAccepted;
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

    /**
     * @return null|string
     */
    public function getLanguage()
    {
        $language = Language::create($this->lang);
        return $language->getName();
    }

    static public function allowedVisibilities():array
    {
        return [
            Visibility::VISIBILITY_GM,
            Visibility::VISIBILITY_FULL
        ];
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

    /**
     * Provides word count that player can see
     * @return int
     */
    public function getWordCount():int
    {
        return $this->getWordCountForPublic();
    }

    /**
     * Provides word count for public part
     * @return int
     */
    public function getWordCountForPublic():int
    {
        return StringHelper::countWords($this->public_text);
    }

    /**
     * Provides word count for private part
     * @return int
     */
    public function getWordCountForPrivate():int
    {
        return StringHelper::countWords($this->private_text);
    }
}
