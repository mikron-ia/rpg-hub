<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "seen_pack".
 *
 * @property string $seen_pack_id
 * @property string $class
 * @property string $name
 *
 * @property Character[] $characters
 * @property CharacterSheet[] $characterSheets
 * @property Epic[] $epics
 * @property Group[] $groups
 * @property Recap[] $recaps
 * @property Seen[] $seens
 * @property Story[] $stories
 */
class SeenPack extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'seen_pack';
    }

    public function rules()
    {
        return [
            [['class', 'name'], 'required'],
            [['class'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 80],
        ];
    }

    public function attributeLabels()
    {
        return [
            'seen_pack_id' => Yii::t('app', 'Seen Pack ID'),
            'class' => Yii::t('app', 'Class'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacters()
    {
        return $this->hasMany(Character::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacterSheets()
    {
        return $this->hasMany(CharacterSheet::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpics()
    {
        return $this->hasMany(Epic::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Group::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecaps()
    {
        return $this->hasMany(Recap::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeens()
    {
        return $this->hasMany(Seen::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::className(), ['seen_pack_id' => 'seen_pack_id']);
    }
}
