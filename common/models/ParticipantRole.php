<?php

namespace common\models;

use Yii;
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
    const ROLE_GM = 'gm';
    const ROLE_PLAYER = 'player';
    const ROLE_ASSISTANT = 'assistant';
    const ROLE_WATCHER = 'watcher';

    public static function tableName()
    {
        return 'participant_role';
    }

    public function rules()
    {
        return [
            [['participant_id', 'role'], 'required'],
            [['participant_id'], 'integer'],
            [['role'], 'string', 'max' => 20],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => Participant::className(), 'targetAttribute' => ['participant_id' => 'participant_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'participant_id' => Yii::t('app', 'PARTICIPANT_ID'),
            'role' => Yii::t('app', 'PARTICIPANT_ROLE'),
        ];
    }

    /**
     * @return string[]
     */
    static public function roleNames()
    {
        return [
            self::ROLE_GM => Yii::t('app', 'PARTICIPANT_ROLE_GM'),
            self::ROLE_PLAYER => Yii::t('app', 'PARTICIPANT_ROLE_PLAYER'),
            self::ROLE_ASSISTANT => Yii::t('app', 'PARTICIPANT_ROLE_ASSISTANT'),
            self::ROLE_WATCHER => Yii::t('app', 'PARTICIPANT_ROLE_WATCHER'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParticipant()
    {
        return $this->hasOne(Participant::className(), ['participant_id' => 'participant_id']);
    }

    public function getRoleDescribed()
    {
        $names = self::roleNames();
        if (isset($names[$this->role])) {
            return $names[$this->role];
        } else {
            return Yii::t('app', 'PARTICIPANT_ROLE_UNKNOWN');
        }
    }
}
