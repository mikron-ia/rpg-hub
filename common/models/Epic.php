<?php

namespace common\models;

use common\models\core\HasParameters;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\HttpException;

/**
 * This is the model class for table "epic".
 *
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $system
 * @property string $parameter_pack_id
 *
 * @property Character[] $characters
 * @property ParameterPack $parameterPack
 * @property User[] $gms
 * @property User[] $players
 * @property Group[] $groups
 * @property Participant[] $participants
 * @property Person[] $people
 * @property Recap[] $recaps
 * @property Story[] $stories
 *
 * @todo: Someday, system field will have to come from a closed list of supported systems
 */
class Epic extends ActiveRecord implements Displayable, HasParameters
{
    use ToolsForEntity;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'epic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'system'], 'required'],
            [['name'], 'string', 'max' => 80],
            [['system'], 'string', 'max' => 20],
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
            'epic_id' => Yii::t('app', 'EPIC_ID'),
            'key' => Yii::t('app', 'EPIC_KEY'),
            'name' => Yii::t('app', 'EPIC_NAME'),
            'system' => Yii::t('app', 'EPIC_GAME_SYSTEM'),
            'parameter_pack_id' => Yii::t('app', 'PARAMETER_PACK'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
        }

        if (empty($this->parameter_pack_id)) {
            $pack = ParameterPack::create('Epic');
            $this->parameter_pack_id = $pack->parameter_pack_id;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacters()
    {
        return $this->hasMany(Character::className(), ['epic_id' => 'epic_id']);
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
    public function getGroups()
    {
        return $this->hasMany(Group::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGms()
    {
        return $this->getParticipants()->joinWith('participantRoles')->onCondition("role = 'gm'");
    }

    /**
     * @return ActiveQuery
     */
    public function getPlayers()
    {
        return $this->getParticipants()->joinWith('participantRoles')->onCondition("role = 'player'");
    }

    /**
     * @return ActiveQuery
     */
    public function getParticipants()
    {
        return $this->hasMany(Participant::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasMany(Person::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRecaps()
    {
        return $this->hasMany(Recap::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return Recap|null
     */
    public function getCurrentRecap()
    {
        $query = new ActiveDataProvider(['query' => $this->getRecaps()->orderBy('time ASC')]);
        $recaps = $query->getModels();

        if ($recaps) {
            $recap = array_pop($recaps);
        } else {
            $recap = null;
        }

        return $recap;
    }

    /**
     * @inheritdoc
     */
    public function getSimpleDataForApi()
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCompleteDataForApi()
    {
        $query = new ActiveDataProvider(['query' => $this->getStories()->orderBy('story_id DESC')]);

        /* @var $stories Story[] */
        $stories = $query->getModels();
        $storyData = [];
        foreach ($stories as $story) {
            $storyData[] = $story->getSimpleDataForApi();
        }

        $recap = $this->getCurrentRecap();
        $recapData = ($recap ? $recap->getCompleteDataForApi() : null);

        return [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
            'current' => $recapData,
            'stories' => $storyData,
        ];
    }

    /**
     * @inheritdoc
     */
    public function isVisibleInApi()
    {
        return true;
    }

    /**
     * Provides list of types allowed by this class
     * @return string[]
     */
    static public function allowedParameterTypes()
    {
        return [
            Parameter::SESSION_COUNT,
            Parameter::PCS_ACTIVE,
            Parameter::CS_ACTIVE,
            Parameter::DATA_SOURCE_FOR_REPUTATION,
            Parameter::EPIC_STATUS,
            Parameter::EPIC_SYSTEM_STATE,
        ];
    }

    /**
     * Determines whether user can create an epic
     * @return bool
     * @throws HttpException
     */
    static public function canUserCreate()
    {
        if (Yii::$app->user->can('openEpic')) {
            return true;
        } else {
            throw new HttpException(401, Yii::t('app', 'NO_RIGHT_TO_CREATE_EPIC'));
        }
    }

    /**
     * Determines whether user can make changes to this epic
     * @return bool
     * @throws HttpException
     */
    public function canUserControlYou()
    {
        if (Yii::$app->user->can('controlEpic', ['epic' => $this])) {
            return true;
        } else {
            throw new HttpException(401, Yii::t('app', 'NO_RIGHT_TO_CONTROL_EPIC'));
        }
    }

    /**
     * Determines whether user can view this epic
     * @return bool
     * @throws HttpException
     */
    public function canUserViewYou()
    {
        if (Yii::$app->user->can('viewEpic', ['epic' => $this])) {
            return true;
        } else {
            throw new HttpException(401, Yii::t('app', 'NO_RIGHT_TO_VIEW_EPIC'));
        }
    }
}
