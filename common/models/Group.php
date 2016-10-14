<?php

namespace common\models;

use common\models\core\HasEpicControl;
use common\models\tools\ToolsForEntity;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "group".
 *
 * @property string $group_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $data
 *
 * @property Epic $epic
 */
class Group extends ActiveRecord implements Displayable, HasEpicControl
{
    use ToolsForEntity;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['epic_id', 'name'], 'required'],
            [['epic_id'], 'integer'],
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
            'group_id' => Yii::t('app', 'GROUP_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'GROUP_KEY'),
            'name' => Yii::t('app', 'GROUP_NAME'),
            'data' => Yii::t('app', 'GROUP_DATA'),
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

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
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
        $decodedData = json_decode($this->data, true);

        $decodedData['name'] = $this->name;
        $decodedData['key'] = $this->key;

        return $decodedData;
    }

    /**
     * @inheritdoc
     */
    public function isVisibleInApi()
    {
        return true;
    }

    static public function canUserIndexThem()
    {
        return self::canUserIndexInEpic(Yii::$app->params['activeEpic'], Yii::t('app', 'NO_RIGHTS_TO_LIST_GROUP'));
    }

    static public function canUserCreateThem()
    {
        return self::canUserCreateInEpic(Yii::$app->params['activeEpic'], Yii::t('app', 'NO_RIGHTS_TO_CREATE_GROUP'));
    }

    public function canUserControlYou()
    {
        return self::canUserControlInEpic($this->epic, Yii::t('app', 'NO_RIGHT_TO_CONTROL_GROUP'));
    }

    public function canUserViewYou()
    {
        return self::canUserViewInEpic($this->epic, Yii::t('app', 'NO_RIGHT_TO_VIEW_GROUP'));
    }
}
