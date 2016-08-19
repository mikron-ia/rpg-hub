<?php

namespace common\models;

use Yii;

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
class Participant extends \yii\db\ActiveRecord
{
    public $roleChoices;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'participant';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'epic_id'], 'required'],
            [['user_id', 'epic_id'], 'integer'],
            [
                ['user_id', 'epic_id'],
                'unique',
                'message' => Yii::t('app', 'ERROR_PARTICIPANT_EXISTS')
            ],
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id']
            ],
            [
                ['epic_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Epic::className(),
                'targetAttribute' => ['epic_id' => 'epic_id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'participant_id' => Yii::t('app', 'PARTICIPANT_ID'),
            'user_id' => Yii::t('app', 'USER_LABEL'),
            'epic_id' => Yii::t('app', 'EPIC_LABEL'),
            'role' => Yii::t('app', 'PARTICIPANT_ROLE'),
            'roleChoices' => Yii::t('app', 'PARTICIPANT_ROLES'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->roleChoices = $this->getRoles();
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (!$this->roleChoices) {
            $this->roleChoices = [];
        }

        $this->setRoles();
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
    public function getParticipantRoles()
    {
        return $this->hasMany(ParticipantRole::className(), ['participant_id' => 'participant_id']);
    }

    public function getRolesList()
    {
        $roles = [];

        foreach ($this->participantRoles as $participantRole) {
            $roles[$participantRole->role] = $participantRole->getRoleDescribed();
        }

        return $roles;
    }

    public function getRoles()
    {
        $roles = [];

        foreach ($this->participantRoles as $participantRole) {
            $roles[$participantRole->role] = $participantRole->role;
        }
        return $roles;
    }

    public function setRoles()
    {
        ParticipantRole::deleteAll(['participant_id' => $this->participant_id]);

        foreach ($this->roleChoices as $roleChoice) {
            $role = new ParticipantRole(['participant_id' => $this->participant_id, 'role' => $roleChoice]);
            $role->save();
        }
    }


}
