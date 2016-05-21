<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "epic".
 *
 * @property string $epic_id
 * @property string $key
 * @property string $name
 * @property string $system
 *
 * @property Character[] $characters
 * @property Group[] $groups
 * @property Person[] $people
 * @property Recap[] $recaps
 * @property Story[] $stories
 */
class Epic extends \yii\db\ActiveRecord implements Displayable
{
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
            [['key', 'name', 'system'], 'required'],
            [['key', 'name'], 'string', 'max' => 80],
            [['system'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'epic_id' => Yii::t('app', 'Epic ID'),
            'key' => Yii::t('app', 'Key'),
            'name' => Yii::t('app', 'Name'),
            'system' => Yii::t('app', 'System'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacters()
    {
        return $this->hasMany(Character::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Group::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasMany(Person::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecaps()
    {
        return $this->hasMany(Recap::className(), ['epic_id' => 'epic_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::className(), ['epic_id' => 'epic_id']);
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
        $basicData = [
            'name' => $this->name,
            'key' => $this->key,
            'help' => [],
        ];
        return $basicData;
    }
}
