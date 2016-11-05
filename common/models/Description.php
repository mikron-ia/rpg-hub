<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasVisibility;
use common\models\core\Language;
use common\models\core\Visibility;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
 *
 * @property DescriptionPack $descriptionPack
 */
class Description extends ActiveRecord implements Displayable, HasVisibility
{
    const TYPE_APPEARANCE = 'appearance';
    const TYPE_HISTORY = 'history';
    const TYPE_PERSONALITY = 'personality';
    const TYPE_WHO = 'who';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'description';
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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
        ];
    }

    /**
     * @return string[]
     */
    static public function typeNames()
    {
        return [
            self::TYPE_APPEARANCE => Yii::t('app', 'DESCRIPTION_TYPE_APPEARANCE'),
            self::TYPE_HISTORY => Yii::t('app', 'DESCRIPTION_TYPE_HISTORY'),
            self::TYPE_PERSONALITY => Yii::t('app', 'DESCRIPTION_TYPE_PERSONALITY'),
            self::TYPE_WHO => Yii::t('app', 'DESCRIPTION_TYPE_WHO'),
        ];
    }

    /**
     * @return string[]
     */
    public function typeNamesForThisClass()
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

    static public function typesForCharacter()
    {
        return [self::TYPE_PERSONALITY];
    }

    static public function typesForPerson()
    {
        return [self::TYPE_APPEARANCE, self::TYPE_HISTORY, self::TYPE_WHO];
    }

    /**
     * @return string[]
     */
    public function allowedTypes()
    {
        return array_keys(self::typeNames());
    }

    /**
     * @return ActiveQuery
     */
    public function getDescriptionPack()
    {
        return $this->hasOne(DescriptionPack::className(), ['description_pack_id' => 'description_pack_id']);
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
        return Markdown::process($this->public_text, 'gfm');
    }

    /**
     * @return string|null
     */
    public function getPrivateFormatted()
    {
        return Markdown::process($this->private_text, 'gfm');
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

    /**
     * @inheritdoc
     */
    public function isVisibleInApi()
    {
        return ($this->visibility === Visibility::VISIBILITY_FULL);
    }
}
