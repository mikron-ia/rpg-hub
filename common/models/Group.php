<?php

namespace common\models;

use Yii;

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
class Group extends \yii\db\ActiveRecord
{
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
            [['epic_id', 'key', 'name', 'data'], 'required'],
            [['epic_id'], 'integer'],
            [['data'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name'], 'string', 'max' => 120],
            [['epic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Epic::className(), 'targetAttribute' => ['epic_id' => 'epic_id']],
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
     * @return \yii\db\ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }
}
