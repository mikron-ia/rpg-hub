<?php

namespace common\models;

use common\behaviours\PerformedActionBehavior;
use common\models\core\HasEpicControl;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Markdown;

/**
 * This is the model class for table "recap".
 *
 * @property string $recap_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $time
 *
 * @property Epic $epic
 */
class Recap extends ActiveRecord implements Displayable, HasEpicControl
{
    use ToolsForEntity;

    public static function tableName()
    {
        return 'recap';
    }

    public function rules()
    {
        return [
            [['epic_id', 'name', 'data', 'time'], 'required'],
            [['epic_id'], 'integer'],
            [['data'], 'string'],
            [['time'], 'safe'],
            [['key'], 'string', 'max' => 80],
            [['name'], 'string', 'max' => 120],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
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
            'time' => Yii::t('app', 'RECAP_TIME'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
        }

        return parent::beforeSave($insert);
    }

    public function behaviors()
    {
        return [
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
    public function getDataFormatted():string
    {
        return Markdown::process($this->data, 'gfm');
    }

    /**
     * @return ActiveQuery
     */
    public function getEpic():ActiveQuery
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
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

    static public function canUserIndexThem()
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic'], Yii::t('app', 'NO_RIGHTS_TO_LIST_RECAP'));
    }

    static public function canUserCreateThem()
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic'], Yii::t('app', 'NO_RIGHTS_TO_CREATE_RECAP'));
    }

    public function canUserControlYou()
    {
        return self::canUserControlInEpic($this->epic, Yii::t('app', 'NO_RIGHT_TO_CONTROL_RECAP'));
    }

    public function canUserViewYou()
    {
        return self::canUserViewInEpic($this->epic, Yii::t('app', 'NO_RIGHT_TO_VIEW_RECAP'));
    }
}
