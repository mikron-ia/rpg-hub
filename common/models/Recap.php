<?php

namespace common\models;

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
class Recap extends ActiveRecord implements Displayable
{
    use ToolsForEntity;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recap';
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->key = $this->generateKey(strtolower((new \ReflectionClass($this))->getShortName()));
        }

        return parent::beforeSave($insert);
    }

    /**
     * Provides recap content formatted in HTML
     * @return string HTML formatted text
     */
    public function getDataFormatted()
    {
        return Markdown::process($this->data, 'gfm');
    }

    /**
     * @return ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
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

        $basicData = [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
            'short' => $this->getDataFormatted(),
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
}
