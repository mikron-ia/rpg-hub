<?php

namespace common\models;

use common\models\core\HasSightings;
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

    /**
     * @var HasSightings
     */
    private $controllingObject;

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
    public function getCharacters(): ActiveQuery
    {
        return $this->hasMany(Character::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCharacterSheets(): ActiveQuery
    {
        return $this->hasMany(CharacterSheet::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEpics(): ActiveQuery
    {
        return $this->hasMany(Epic::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(Group::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRecaps(): ActiveQuery
    {
        return $this->hasMany(Recap::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSightings(): ActiveQuery
    {
        return $this->hasMany(Seen::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSightingsForNotices(): ActiveQuery
    {
        return $this
            ->hasMany(Seen::class, ['seen_pack_id' => 'seen_pack_id'])
            ->where(new Expression('seen_at IS NULL'));
    }

    /**
     * @return ActiveQuery
     */
    public function getSightingsForSightings()
    {
        return $this
            ->hasMany(Seen::class, ['seen_pack_id' => 'seen_pack_id'])
            ->where(new Expression('seen_at IS NOT NULL'));
    }

    /**
     * @param string $status
     * @return ActiveQuery
     */
    public function getSightingsWithStatus(string $status): ActiveQuery
    {
        return $this
            ->hasMany(Seen::class, ['seen_pack_id' => 'seen_pack_id'])
            ->where(['status' => $status]);
    }

    /**
     * @return ActiveQuery
     */
    public function getSightingsForCurrentUser(): ActiveQuery
    {
        return $this
            ->hasMany(Seen::class, ['seen_pack_id' => 'seen_pack_id'])
            ->where(['user_id' => Yii::$app->user->id]);
    }

    /**
     * @param $userId
     *
     * @return ActiveQuery
     */
    public function getSightingsForUser($userId): ActiveQuery
    {
        return $this
            ->hasMany(Seen::class, ['seen_pack_id' => 'seen_pack_id'])
            ->where(['user_id' => $userId]);
    }

    /**
     * @return ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * Creates a new, empty record
     * @param int $userId
     * @return Seen|null
     * @todo Update return value once PHP 7.1 is used
     */
    public function createRecordForUser($userId)
    {
        $record = new Seen();
        $record->user_id = $userId;
        $record->seen_pack_id = $this->seen_pack_id;
        $record->alert_threshold = 0;
        if ($record->save()) {
            $record->refresh();
            return $record;
        } else {
            return null;
        }
    }

    /**
     * @param bool $fullSight Has user seen all data? True for views, false for indexing
     * @return bool Success of the operation
     */
    public function recordSighting(bool $fullSight = true): bool
    {
        if (Yii::$app instanceof ConsoleApplication) {
            /* There is no point to record sighting from a console */
            return false;
        }

        if (Yii::$app->user->isGuest) {
            $userId = null;
        } else {
            $userId = Yii::$app->user->identity->getId();
        }

        $foundRecord = Seen::findOne([
            'seen_pack_id' => $this->seen_pack_id,
            'user_id' => $userId,
        ]);

        if ($foundRecord) {
            $record = $foundRecord;
        } else {
            $record = $this->createRecordForUser($userId);
        }

        if ($record) {
            $record->noted_at = time();
            if ($fullSight) {
                $record->seen_at = time();
                $record->status = Seen::STATUS_SEEN;
            }

            return $record->save();
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function updateRecord(): bool
    {
        $foundRecords = Seen::findAll([
            'seen_pack_id' => $this->seen_pack_id,
        ]);

        $updateResult = true;

        foreach ($foundRecords as $record) {
            if ($record->status != Seen::STATUS_NEW) {
                $record->status = Seen::STATUS_UPDATED;
                $updateResult = $updateResult && $record->save();
            }
        }

        return $updateResult;
    }

    /**
     * @return bool
     */
    public function recordNotification(): bool
    {
        return $this->recordSighting(false);
    }

    /**
     * @return HasSightings
     */
    public function getControllingObject(): HasSightings
    {
        if (empty($this->controllingObject)) {
            $className = 'common\models\\' . $this->class;
            $this->controllingObject = ($className)::findOne(['seen_pack_id' => $this->seen_pack_id]);
        }

        return $this->controllingObject;
    }

    /**
     * @return Epic
     */
    public function getEpic(): Epic
    {
        return $this->getControllingObject()->getEpic()->one();
    }

    /**
     * Create sighting packs for listed participants
     * Intended for use with Participant list from Epic
     * @param Participant[] $participants
     * @return bool
     */
    public function createPacksForParticipants(array $participants): bool
    {
        $result = true;
        foreach ($participants as $participant) {
            $result = $result && $this->createRecordForUser($participant->user_id);
        }
        return $result;
    }

    /**
     * Creates new Sighting objects for users that do not have them
     * @return bool
     */
    public function createAbsentSightingObjects(): bool
    {
        $users = $this->getEpic()->participants;
        $sightingsRaw = Seen::findAll(['seen_pack_id' => $this->seen_pack_id]);
        $sightingsOrdered = [];

        foreach ($sightingsRaw as $sighting) {
            $sightingsOrdered[$sighting->user_id] = $sighting;
        }

        $result = true;

        foreach ($users as $user) {
            if (!isset($sightingsOrdered[$user->user_id])) {
                $sighting = new Seen();
                $sighting->user_id = $user->user_id;
                $sighting->seen_pack_id = $this->seen_pack_id;
                $sighting->status = Seen::STATUS_NEW;

                $saveResult = $sighting->save();
                $result = $result && $saveResult;
            }
        }
        return $result;
    }

    /**
     * @param string $class
     * @return SeenPack
     */
    public static function create(string $class): SeenPack
    {
        $pack = new SeenPack(['class' => $class]);

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    /**
     * Fills missing sightings for current user
     */
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

    /**
     * @return string
     */
    public function getStatusForCurrentUser(): string
    {
        $this->fillSightingForCurrentUser();

        if (!$this->sightingForCurrentUser) {
            $names = Seen::statusNames();
            return $names[Seen::STATUS_NEW];
        } else {
            return $this->sightingForCurrentUser->getName();
        }
    }

    /**
     * @return string
     */
    public function getCSSForCurrentUser(): string
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
