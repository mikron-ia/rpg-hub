<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "participant".
 *
 * @property integer $participant_id
 * @property string $user_id
 * @property string $epic_id
 *
 * @property User $user
 * @property Epic $epic
 * @property ParticipantRole[] $participantRoles
 */
class Participant extends ActiveRecord
{
    public array $roleChoices = [];

    public static function tableName(): string
    {
        return 'participant';
    }

    public function rules()
    {
        return [
            [['user_id', 'epic_id'], 'required'],
            [['user_id', 'epic_id'], 'integer'],
            [['roleChoices'], 'safe'],
            [
                ['user_id', 'epic_id'],
                'unique',
                'targetAttribute' => ['user_id', 'epic_id'],
                'comboNotUnique' => Yii::t('app', 'ERROR_PARTICIPANT_EXISTS')
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
        ];
    }

    /**
     * @return array<string,string>
     */
    public function attributeLabels(): array
    {
        return [
            'participant_id' => Yii::t('app', 'PARTICIPANT_ID'),
            'user_id' => Yii::t('app', 'USER_LABEL'),
            'epic_id' => Yii::t('app', 'EPIC_LABEL'),
            'role' => Yii::t('app', 'PARTICIPANT_ROLE'),
            'roleChoices' => Yii::t('app', 'PARTICIPANT_ROLES'),
        ];
    }

    public function afterFind(): void
    {
        $this->roleChoices = $this->getRoles();

        parent::afterFind();
    }

    public function afterSave($insert, $changedAttributes): void
    {
        if (!$this->roleChoices) {
            $this->roleChoices = [];
        }

        $this->setRoles();

        parent::afterSave($insert, $changedAttributes);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getEpic(): ActiveQuery
    {
        return $this->hasOne(Epic::class, ['epic_id' => 'epic_id']);
    }

    public function getParticipantRoles(): ActiveQuery
    {
        return $this->hasMany(ParticipantRole::class, ['participant_id' => 'participant_id']);
    }

    /**
     * @return array<string,string>
     */
    public function getRolesList(): array
    {
        $roles = [];

        foreach ($this->participantRoles as $participantRole) {
            $roles[$participantRole->role] = $participantRole->getRoleDescribed();
        }

        return $roles;
    }

    /**
     * @return array<string,string>
     */
    public function getRoles(): array
    {
        $roles = [];

        foreach ($this->participantRoles as $participantRole) {
            $roles[$participantRole->role] = $participantRole->role;
        }
        return $roles;
    }

    public function setRoles(): void
    {
        ParticipantRole::deleteAll(['participant_id' => $this->participant_id]);

        foreach ($this->roleChoices as $roleChoice) {
            $role = new ParticipantRole(['participant_id' => $this->participant_id, 'role' => $roleChoice]);
            $role->save();
        }
    }

    /**
     * Informs whether a given user is a participant of a given epic
     */
    static public function participantExists(User $user, Epic $epic): bool
    {
        if (Participant::findOne(['user_id' => $user->id, 'epic_id' => $epic->epic_id])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Informs whether a given user has a given role in a given epic
     */
    static public function participantHasRole(User $user, Epic $epic, string $role): bool
    {
        $participant = Participant::find()
            ->joinWith('participantRoles')
            ->andWhere([
                'user_id' => $user->id,
                'epic_id' => $epic->epic_id,
                'role' => $role
            ]);

        if ($participant->one()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Creates Game Master participant for an epic
     *
     * @throws Exception
     */
    static public function createForEpic(int $epic_id, int $user_id, string $role): bool
    {
        $participant = new Participant();
        $participant->epic_id = $epic_id;
        $participant->user_id = $user_id;
        $participant->roleChoices = [$role];
        return $participant->save();
    }
}
