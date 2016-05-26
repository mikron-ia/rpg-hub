<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "person".
 *
 * @property string $person_id
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $tagline
 * @property string $data
 * @property string $visibility
 * @property string $character_id
 *
 * @property Epic $epic
 * @property Character $character
 */
class Person extends \yii\db\ActiveRecord implements Displayable
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'person';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['epic_id', 'key', 'name', 'tagline', 'data'], 'required'],
            [['epic_id', 'character_id'], 'integer'],
            [['data', 'visibility'], 'string'],
            [['key'], 'string', 'max' => 80],
            [['name', 'tagline'], 'string', 'max' => 120],
            [['epic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Epic::className(), 'targetAttribute' => ['epic_id' => 'epic_id']],
            [['character_id'], 'exist', 'skipOnError' => true, 'targetClass' => Character::className(), 'targetAttribute' => ['character_id' => 'character_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'person_id' => Yii::t('app', 'PERSON_ID'),
            'epic_id' => Yii::t('app', 'LABEL_EPIC'),
            'key' => Yii::t('app', 'PERSON_KEY'),
            'name' => Yii::t('app', 'PERSON_NAME'),
            'tagline' => Yii::t('app', 'PERSON_TAGLINE'),
            'data' => Yii::t('app', 'PERSON_DATA'),
            'visibility' => Yii::t('app', 'PERSON_VISIBILITY'),
            'character_id' => Yii::t('app', 'LABEL_CHARACTER'),
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
    public function getCharacter()
    {
        return $this->hasOne(Character::className(), ['character_id' => 'character_id']);
    }

    /**
     * @inheritdoc
     */
    public function getSimpleData()
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
            'tagline' => $this->tagline,
            'tags' => [],
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
        $decodedData['tagline'] = $this->tagline;

        return $decodedData;
    }
}
