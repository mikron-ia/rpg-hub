<?php

namespace common\models;

use phpDocumentor\Reflection\DocBlock\Tags\See;
use yii\console\Application as ConsoleApplication;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "seen_pack".
 *
 * @property string $seen_pack_id
 * @property string $class
 *
 * @property Character[] $characters
 * @property CharacterSheet[] $characterSheets
 * @property Epic[] $epics
 * @property Group[] $groups
 * @property Recap[] $recaps
 * @property Seen[] $sightings
 * @property Story[] $stories
 */
class SeenPack extends ActiveRecord
{
    /**
     * @var Seen
     */
    private $sightingForCurrentUser;

    public static function tableName()
    {
        return 'seen_pack';
    }

    public function rules()
    {
        return [
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'seen_pack_id' => Yii::t('app', 'SEEN_PACK_ID'),
            'class' => Yii::t('app', 'SEEN_PACK_CLASS'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacters()
    {
        return $this->hasMany(Character::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacterSheets()
    {
        return $this->hasMany(CharacterSheet::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpics()
    {
        return $this->hasMany(Epic::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Group::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRecaps()
    {
        return $this->hasMany(Recap::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSightings()
    {
        return $this->hasMany(Seen::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSightingsForNotices()
    {
        return $this
            ->hasMany(Seen::className(), ['seen_pack_id' => 'seen_pack_id'])
            ->where(new Expression('seen_at IS NULL'));;
    }

    /**
     * @return ActiveQuery
     */
    public function getSightingsForSightings()
    {
        return $this
            ->hasMany(Seen::className(), ['seen_pack_id' => 'seen_pack_id'])
            ->where(new Expression('seen_at IS NOT NULL'));
    }

    /**
     * @param string $status
     * @return ActiveQuery
     */
    public function getSightingsWithStatus(string $status)
    {
        return $this
            ->hasMany(Seen::className(), ['seen_pack_id' => 'seen_pack_id'])
            ->where(['status' => $status]);
    }

    /**
     * @return ActiveQuery
     */
    public function getSightingsForCurrentUser()
    {
        return $this
            ->hasMany(Seen::className(), ['seen_pack_id' => 'seen_pack_id'])
            ->where(['user_id' => Yii::$app->user->id]);
    }

    /**
     * @return ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::className(), ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @param bool $fullSight Has user seen all data? True for views, false for indexing
     * @return bool Success of the operation
     */
    public function recordSighting(bool $fullSight = true):bool
    {
        if (Yii::$app instanceof ConsoleApplication) {
            /* There is no point to record sighting from a console */
            return false;
        }

        $userId = Yii::$app->user->identity->getId();

        $foundRecord = Seen::findOne([
            'seen_pack_id' => $this->seen_pack_id,
            'user_id' => $userId,
        ]);

        if ($foundRecord) {
            $record = $foundRecord;
        } else {
            $record = new Seen();
            $record->user_id = $userId;
            $record->seen_pack_id = $this->seen_pack_id;
            $record->alert_threshold = 0;
        }

        $record->noted_at = time();
        if ($fullSight) {
            $record->seen_at = time();
            $record->status = Seen::STATUS_SEEN;
        }

        return $record->save();
    }

    public function updateRecord()
    {
        $foundRecords = Seen::findAll([
            'seen_pack_id' => $this->seen_pack_id,
        ]);

        foreach ($foundRecords as $record) {
            if ($record->status != Seen::STATUS_NEW) {
                $record->status = Seen::STATUS_UPDATED;
                $record->save();
            }
        }
    }

    /**
     * @return bool
     */
    public function recordNotification():bool
    {
        return $this->recordSighting(false);
    }

    /**
     * @param string $class
     * @return SeenPack
     */
    public static function create(string $class):SeenPack
    {
        $pack = new SeenPack(['class' => $class]);

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    private function fillSightingForCurrentUser()
    {
        if (!$this->sightingForCurrentUser) {
            $userId = Yii::$app->user->identity->getId();

            $sighting = Seen::findOne([
                'seen_pack_id' => $this->seen_pack_id,
                'user_id' => $userId,
            ]);

            if ($sighting) {
                $this->sightingForCurrentUser = $sighting;
            } else {
                $this->sightingForCurrentUser = null;
            }
        }
    }

    public function getStatusForCurrentUser():string
    {
        $this->fillSightingForCurrentUser();

        if (!$this->sightingForCurrentUser) {
            $names = Seen::statusNames();
            return $names[Seen::STATUS_NEW];
        } else {
            return $this->sightingForCurrentUser->getName();
        }
    }

    public function getCSSForCurrentUser():string
    {
        $this->fillSightingForCurrentUser();

        if (!$this->sightingForCurrentUser) {
            $names = Seen::statusCSS();
            return $names[Seen::STATUS_NEW];
        } else {
            return $this->sightingForCurrentUser->getCSS();
        }
    }
}
