<?php

namespace common\models;

use common\models\core\HasKey;
use common\models\tools\ToolsForEntity;
use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\HttpException;

/**
 * This is the model class for table "participant".
 *
 * @property integer $participant_id
 * @property string $key
 * @property string $user_id
 * @property string $epic_id
 *
 * @property User $user
 * @property Epic $epic
 * @property ParticipantRole[] $participantRoles
 */
class Participant extends ActiveRecord implements HasKey
{
    use ToolsForEntity;

    public array|string $roleChoices = [];

    #[Override]
    public static function tableName(): string
    {
        return 'participant';
    }

    #[Override]
    public static function keyParameterName(): string
    {
        return 'participant';
    }


    #[Override]
    public function rules(): array
    {
        return [
            [['user_id', 'epic_id'], 'required'],
            [['user_id', 'epic_id'], 'integer'],
            [['roleChoices'], 'safe'],
            [
                ['user_id', 'epic_id'],
                'unique',
                'targetAttribute' => ['user_id', 'epic_id'],
                'comboNotUnique' => Yii::t('app', 'ERROR_PARTICIPANT_EXISTS'),
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::class,
                'targetAttribute' => ['epic_id' => 'epic_id'],
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
            'key' => Yii::t('app', 'PARTICIPANT_KEY'),
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

    /**
     * @throws Exception
     */
    #[Override]
    public function afterSave($insert, $changedAttributes): void
    {
        if (!$this->roleChoices) {
            $this->roleChoices = [];
        }

        $this->setRoles();

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @throws HttpException
     */
    #[Override]
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->key = $this->generateKey();
        }

        return parent::beforeSave($insert);
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

    /**
     * @throws Exception
     */
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
    public static function participantExists(User $user, Epic $epic): bool
    {
        return (bool)Participant::findOne(['user_id' => $user->id, 'epic_id' => $epic->epic_id]);
    }

    /**
     * Informs whether a given user has a given role in a given epic
     */
    public static function participantHasRole(User $user, Epic $epic, string $role): bool
    {
        $participant = Participant::find()
            ->joinWith('participantRoles')
            ->andWhere([
                'user_id' => $user->id,
                'epic_id' => $epic->epic_id,
                'role' => $role,
            ]);

        return (bool)$participant->one();
    }

    /**
     * Creates Game Master participant for an epic
     *
     * @throws Exception
     */
    public static function createForEpic(int $epic_id, int $user_id, string $role): bool
    {
        $participant = new Participant();

        $participant->epic_id = $epic_id;
        $participant->user_id = $user_id;
        $participant->roleChoices = [$role];

        return $participant->save();
    }
}
