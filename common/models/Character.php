<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "character".
 *
 * @property string $character_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $currently_delivered_person_id
 *
 * @property Epic $epic
 * @property Person $currentlyDeliveredPerson
 * @property Person[] $people
 */
class Character extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'character';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['epic_id', 'key', 'name', 'data'], 'required'],
            [['epic_id', 'currently_delivered_person_id'], 'integer'],
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
                ['currently_delivered_person_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Person::className(),
                'targetAttribute' => [
                    'currently_delivered_person_id' => 'person_id'
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'character_id' => Yii::t('app', 'CHARACTER_ID'),
            'epic_id' => Yii::t('app', 'EPIC_LABEL'),
            'key' => Yii::t('app', 'CHARACTER_KEY'),
            'name' => Yii::t('app', 'CHARACTER_NAME'),
            'data' => Yii::t('app', 'CHARACTER_DATA'),
            'currently_delivered_person_id' => Yii::t('app', 'CHARACTER_DELIVERED_PERSON_ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpic()
    {
        return $this->hasOne(Epic::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrentlyDeliveredPerson()
    {
        return $this->hasOne(Person::className(), ['person_id' => 'currently_delivered_person_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasMany(Person::className(), ['character_id' => 'character_id']);
    }

    /**
     * @inheritdoc
     */
    public function getSimpleData()
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCompleteData()
    {
        $decodedData = json_decode($this->data, true);

        $decodedData['name'] = $this->name;
        $decodedData['key'] = $this->key;

        return $decodedData;
    }
}
