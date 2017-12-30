<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\core\HasSightings;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Markdown;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "recap".
 *
 * @property string $recap_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $seen_pack_id
 * @property string $point_in_time_id
 * @property int $position
 * @property string $utility_bag_id
 *
 * @property Epic $epic
 * @property PointInTime $pointInTime
 * @property SeenPack $seenPack
 * @property UtilityBag $utilityBag
 */
class Recap extends ActiveRecord implements Displayable, HasEpicControl, HasSightings
{
    use ToolsForEntity;

    public static function tableName()
    {
        return 'recap';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name', 'data'], 'required'],
            [['epic_id', 'point_in_time_id', 'position'], 'integer'],
            [['data'], 'string'],
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
                ['point_in_time_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => PointInTime::className(),
                'targetAttribute' => ['point_in_time_id' => 'point_in_time_id']
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'recap_id' => Yii::t('app', 'RECAP_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'RECAP_KEY'),
            'name' => Yii::t('app', 'RECAP_NAME'),
            'data' => Yii::t('app', 'RECAP_DATA'),
            'point_in_time_id' => Yii::t('app', 'LABEL_POINT_IN_TIME'),
            'pointInTime' => Yii::t('app', 'LABEL_POINT_IN_TIME'),
            'position' => Yii::t('app', 'RECAP_POSITION'),
            'utility_bag_id' => Yii::t('app', 'UTILITY_BAG'),
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
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
        }

        if (empty($this->seen_pack_id)) {
            $pack = SeenPack::create('Recap');
            $this->seen_pack_id = $pack->seen_pack_id;
        }

        if (empty($this->utility_bag_id)) {
            $pack = UtilityBag::create('Recap');
            $this->utility_bag_id = $pack->utility_bag_id;
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
            'performedActionBehavior' => [
                'class' => PerformedActionBehavior::className(),
                'idName' => 'recap_id',
                'className' => 'Recap',
            ]
        ];
    }

    /**
     * Provides recap content formatted in HTML
     * @return string HTML formatted text
     */
    public function getDataFormatted(): string
    {
        return Markdown::process($this->data, 'gfm');
    }

    /**
     * @return ActiveQuery
     */
    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPointInTime(): ActiveQuery
    {
        return $this->hasOne(PointInTime::className(), ['point_in_time_id' => 'point_in_time_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSeenPack(): ActiveQuery
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
        $basicData = [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
            'short' => $this->getDataFormatted(),
        ];
        return $basicData;
    }

    public function isVisibleInApi()
    {
        return true;
    }

    static public function canUserIndexThem(): bool
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic']);
    }

    static public function canUserCreateThem(): bool
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic']);
    }

    public function canUserControlYou(): bool
    {
        return self::canUserControlInEpic($this->epic);
    }

    public function canUserViewYou(): bool
    {
        return self::canUserViewInEpic($this->epic);
    }

    static function throwExceptionAboutCreate()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_CREATE_RECAP'));
    }

    static function throwExceptionAboutControl()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_CONTROL_RECAP'));
    }

    static function throwExceptionAboutIndex()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHTS_TO_LIST_RECAP'));
    }

    static function throwExceptionAboutView()
    {
        self::thrownExceptionAbout(Yii::t('app', 'NO_RIGHT_TO_VIEW_RECAP'));
    }

    public function recordSighting(): bool
    {
        return $this->seenPack->recordSighting();
    }

    public function recordNotification(): bool
    {
        return $this->seenPack->recordNotification();
    }

    public function showSightingStatus(): string
    {
        return $this->seenPack->getStatusForCurrentUser();
    }

    public function showSightingCSS(): string
    {
        return $this->seenPack->getCSSForCurrentUser();
    }
}
