<?php

namespace common\models;

use common\models\core\HasSightings;
use common\models\core\SeenStatus;
use yii\console\Application as ConsoleApplication;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
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
    private ?Seen $sightingForCurrentUser;
    private ?HasSightings $controllingObject;

    public static function tableName(): string
    {
        return 'seen_pack';
    }

    public function rules(): array
    {
        return [
            [['class'], 'required'],
            [['class'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'seen_pack_id' => Yii::t('app', 'SEEN_PACK_ID'),
            'class' => Yii::t('app', 'SEEN_PACK_CLASS'),
        ];
    }

    public function getCharacters(): ActiveQuery
    {
        return $this->hasMany(Character::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getCharacterSheets(): ActiveQuery
    {
        return $this->hasMany(CharacterSheet::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getEpics(): ActiveQuery
    {
        return $this->hasMany(Epic::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(Group::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getRecaps(): ActiveQuery
    {
        return $this->hasMany(Recap::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getSightings(): ActiveQuery
    {
        return $this->hasMany(Seen::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    public function getSightingsForNotices(): ActiveQuery
    {
        return $this
            ->hasMany(Seen::class, ['seen_pack_id' => 'seen_pack_id'])
            ->where(new Expression('seen_at IS NULL'));
    }

    public function getSightingsForSightings(): ActiveQuery
    {
        return $this
            ->hasMany(Seen::class, ['seen_pack_id' => 'seen_pack_id'])
            ->where(new Expression('seen_at IS NOT NULL'));
    }

    public function getSightingsWithStatus(SeenStatus $status): ActiveQuery
    {
        return $this
            ->hasMany(Seen::class, ['seen_pack_id' => 'seen_pack_id'])
            ->where(['status' => $status->value]);
    }

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

    public function getStories(): ActiveQuery
    {
        return $this->hasMany(Story::class, ['seen_pack_id' => 'seen_pack_id']);
    }

    /**
     * Creates a new, empty record
     *
     * @param int|null $userId
     *
     * @return Seen|null
     *
     * @throws Exception
     */
    public function createRecordForUser(?int $userId): ?Seen
    {
        $record = new Seen();
        $record->user_id = $userId;
        $record->seen_pack_id = $this->seen_pack_id;
        $record->alert_threshold = 0;
        if ($record->save()) {
            $record->refresh();
            return $record;
        }

        return null;
    }


    /**
     * @param bool $fullSight Has the user seen all data? True for views, false for indexing
     * @param ImportancePack|null $importancePack Importance pack to flag in case of status change
     *
     * @return bool
     *
     * @throws Exception
     */
    private function recordNotificationOrSighting(bool $fullSight, ?ImportancePack $importancePack = null): bool
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
                if ($importancePack !== null && $record->getSeenStatus() !== SeenStatus::STATUS_SEEN) {
                    // todo This would be a great spot to throw an event, if there was anything to catch it
                    $importancePack->flagForRecalculation(); // flag only if possible and on status change
                }
                $record->seen_at = time();
                $record->setSeenStatus(SeenStatus::STATUS_SEEN);
            }

            return $record->save();
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function updateRecord(): bool
    {
        $foundRecords = Seen::findAll([
            'seen_pack_id' => $this->seen_pack_id,
        ]);

        $updateResult = true;

        foreach ($foundRecords as $record) {
            if ($record->getSeenStatus() != SeenStatus::STATUS_NEW) {
                $record->setSeenStatus(SeenStatus::STATUS_UPDATED);
                $updateResult = $updateResult && $record->save();
            }
        }

        return $updateResult;
    }

    /**
     * @throws Exception
     */
    public function recordNotification(): bool
    {
        return $this->recordNotificationOrSighting(false);
    }

    /**
     * @throws Exception
     */
    public function recordSighting(?ImportancePack $importancePack = null): bool
    {
        return $this->recordNotificationOrSighting(true, $importancePack);
    }

    public function getControllingObject(): HasSightings
    {
        if (empty($this->controllingObject)) {
            $className = 'common\models\\' . $this->class;
            $this->controllingObject = ($className)::findOne(['seen_pack_id' => $this->seen_pack_id]);
        }

        return $this->controllingObject;
    }

    public function getEpic(): Epic
    {
        return $this->getControllingObject()->getEpic()->one();
    }

    /**
     * Create sighting packs for listed participants
     * Intended for use with the Participant list from Epic
     *
     * @param Participant[] $participants
     *
     * @throws Exception
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
     *
     * @throws Exception
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
                $sighting->status = SeenStatus::STATUS_NEW->value;

                $saveResult = $sighting->save();
                $result = $result && $saveResult;
            }
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    public static function create(string $class): SeenPack
    {
        $pack = new SeenPack(['class' => $class]);

        $pack->save();
        $pack->refresh();

        return $pack;
    }

    /**
     * Fills missing sightings for the current user
     */
    private function fillSightingForCurrentUser(): void
    {
        if (empty($this->sightingForCurrentUser)) {
            $userId = Yii::$app->user->identity->getId();

            $sighting = Seen::findOne([
                'seen_pack_id' => $this->seen_pack_id,
                'user_id' => $userId,
            ]);

            $this->sightingForCurrentUser = null;
            if ($sighting) {
                $this->sightingForCurrentUser = $sighting;
            }
        }
    }

    public function getStatusForCurrentUser(): string
    {
        $this->fillSightingForCurrentUser();

        if (empty($this->sightingForCurrentUser)) {
            $names = Seen::statusNames();
            return $names[SeenStatus::STATUS_NEW->value];
        }

        return $this->sightingForCurrentUser->getName();
    }

    public function getCSSForCurrentUser(): string
    {
        $this->fillSightingForCurrentUser();

        if (empty($this->sightingForCurrentUser)) {
            return SeenStatus::STATUS_NEW->statusCSS();
        }

        return $this->sightingForCurrentUser->getCSS();
    }
}
