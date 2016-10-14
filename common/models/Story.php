<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\core\HasParameters;
use common\models\core\Visibility;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Markdown;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "story".
 *
 * @property string $story_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $short
 * @property string $long
 * @property int $position
 * @property string $data
 * @property string $parameter_pack_id
 *
 * @property Epic $epic
 * @property ParameterPack $parameterPack
 * @property StoryParameter[] $storyParameters
 */
class Story extends ActiveRecord implements Displayable, HasParameters, HasEpicControl
{
    use ToolsForEntity;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'story';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['epic_id', 'key', 'name', 'short', 'data'], 'required'],
            [['epic_id', 'position'], 'integer'],
            [['short', 'long', 'data'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name'], 'string', 'max' => 120],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
            [
                ['parameter_pack_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => ParameterPack::className(),
                'targetAttribute' => ['parameter_pack_id' => 'parameter_pack_id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'story_id' => Yii::t('app', 'STORY_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'STORY_KEY'),
            'name' => Yii::t('app', 'STORY_NAME'),
            'short' => Yii::t('app', 'STORY_SHORT'),
            'long' => Yii::t('app', 'STORY_LONG'),
            'position' => Yii::t('app', 'STORY_POSITION'),
            'data' => Yii::t('app', 'STORY_DATA'),
            'parameter_pack_id' => Yii::t('app', 'PARAMETER_PACK'),
            'storyParameters' => Yii::t('app', 'STORY_PARAMETERS'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
            $this->data = json_encode([]);
        }

        if (empty($this->parameter_pack_id)) {
            $pack = ParameterPack::create('Story');
            $this->parameter_pack_id = $pack->parameter_pack_id;
        }

        return parent::beforeSave($insert);
    }

    public function behaviors()
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'positionAttribute' => 'position',
                'groupAttributes' => ['epic_id'],
            ],
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
    public function getParameterPack()
    {
        return $this->hasOne(ParameterPack::className(), ['parameter_pack_id' => 'parameter_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStoryParameters()
    {
        return $this->hasMany(StoryParameter::className(), ['story_id' => 'story_id']);
    }


    /**
     * Provides story summary formatted in HTML
     * @return string Short summary formatted to HTML
     */
    public function getShortFormatted()
    {
        return Markdown::process($this->short, 'gfm');
    }

    /**
     * Provides story summary formatted in HTML
     * @return string Long summary formatted to HTML
     */
    public function getLongFormatted()
    {
        return Markdown::process($this->long, 'gfm');
    }

    public function formatParameters()
    {
        $parameters = [];

        foreach ($this->parameterPack->parameters as $parameter) {
            if ($parameter->visibility == Visibility::VISIBILITY_FULL) {
                $parameters[] = [
                    'name' => $parameter->getCodeName(),
                    'value' => $parameter->content,
                ];
            }
        }

        return $parameters;
    }

    /**
     * @inheritdoc
     */
    public function getSimpleDataForApi()
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
            'parameters' => $this->formatParameters(),
            'short' => $this->getShortFormatted(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCompleteDataForApi()
    {

        $basicData = [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
            'parameters' => $this->formatParameters(),
            'short' => $this->getShortFormatted(),
            'long' => $this->getLongFormatted(),
        ];
        return $basicData;
    }

    /**
     * @inheritdoc
     */
    public function isVisibleInApi()
    {
        return true;
    }

    static public function allowedParameterTypes()
    {
        return [
            Parameter::STORY_NUMBER,
            Parameter::TIME_RANGE,
            Parameter::LOCATION_POINT_START,
            Parameter::LOCATION_POINT_END,
            Parameter::SESSION_COUNT,
            Parameter::XP_PARTY,
            Parameter::PCS_ACTIVE,
            Parameter::CS_ACTIVE,
        ];
    }

    static public function canUserIndexThem()
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic'], Yii::t('app', 'NO_RIGHTS_TO_LIST_STORY'));
    }

    static public function canUserCreateThem()
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic'], Yii::t('app', 'NO_RIGHTS_TO_CREATE_STORY'));
    }

    public function canUserControlYou()
    {
        return self::canUserControlInEpic($this->epic, Yii::t('app', 'NO_RIGHT_TO_CONTROL_STORY'));
    }

    public function canUserViewYou()
    {
        return self::canUserViewInEpic($this->epic, Yii::t('app', 'NO_RIGHT_TO_VIEW_STORY'));
    }
}
