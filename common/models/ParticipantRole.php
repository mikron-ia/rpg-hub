<?php

namespace common\models;

use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "participant_role".
 *
 * @property string $participant_id
 * @property string $role
 *
 * @property Participant $participant
 */
final class ParticipantRole extends ActiveRecord
{
    const string ROLE_GM = 'gm';
    const string ROLE_PLAYER = 'player';
    const string ROLE_ASSISTANT = 'assistant';
    const string ROLE_WATCHER = 'watcher';
    const string ROLE_MANAGER = 'manager';

    #[Override]
    public static function tableName(): string
    {
        return 'participant_role';
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['participant_id', 'role'], 'required'],
            [['participant_id'], 'integer'],
            [['role'], 'string', 'max' => 20],
            [
                ['participant_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Participant::class,
                'targetAttribute' => ['participant_id' => 'participant_id'],
            ],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'participant_id' => Yii::t('app', 'PARTICIPANT_ID'),
            'role' => Yii::t('app', 'PARTICIPANT_ROLE'),
        ];
    }

    /**
     * @return array<string,string>
     */
    static public function roleNames(): array
    {
        return [
            self::ROLE_GM => Yii::t('app', 'PARTICIPANT_ROLE_GM'),
            self::ROLE_PLAYER => Yii::t('app', 'PARTICIPANT_ROLE_PLAYER'),
            self::ROLE_ASSISTANT => Yii::t('app', 'PARTICIPANT_ROLE_ASSISTANT'),
            self::ROLE_WATCHER => Yii::t('app', 'PARTICIPANT_ROLE_WATCHER'),
            self::ROLE_MANAGER => Yii::t('app', 'PARTICIPANT_ROLE_MANAGER'),
        ];
    }

    public function getParticipant(): ActiveQuery
    {
        return $this->hasOne(Participant::class, ['participant_id' => 'participant_id']);
    }

    /**
     * Provides a description of the role
     */
    public function getRoleDescribed(): string
    {
        $names = self::roleNames();
        if (isset($names[$this->role])) {
            return $names[$this->role];
        }

        return Yii::t('app', 'PARTICIPANT_ROLE_UNKNOWN');
    }
}
