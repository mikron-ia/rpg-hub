<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_invitation".
 *
 * @property string $id
 * @property string $email
 * @property string $status
 * @property string $created_by
 * @property integer $created_at
 * @property integer $opened_at
 * @property integer $used_at
 * @property integer $revoked_at
 * @property integer $valid_to
 * @property string $token
 * @property string $message
 * @property string $intended_role
 * @property string $note
 * @property string $language
 *
 * @property User $createdBy
 */
class UserInvitation extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_invitation';
    }

    public function rules()
    {
        return [
            [['email', 'message', 'intended_role'], 'required'],
            [['created_by', 'revoked_at'], 'integer'],
            [['message'], 'string'],
            [['email', 'status', 'token', 'note'], 'string', 'max' => 255],
            ['intended_role', 'in', 'range' => User::allowedUserRoles()],
            [['language'], 'string', 'max' => 5],
            [['token'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'USER_INVITATION_ID'),
            'email' => Yii::t('app', 'USER_INVITATION_EMAIL'),
            'status' => Yii::t('app', 'USER_INVITATION_STATUS'),
            'created_by' => Yii::t('app', 'USER_INVITATION_CREATOR'),
            'created_at' => Yii::t('app', 'USER_INVITATION_CREATED_AT'),
            'opened_at' => Yii::t('app', 'USER_INVITATION_OPENED_AT'),
            'used_at' => Yii::t('app', 'USER_INVITATION_USED_AT'),
            'revoked_at' => Yii::t('app', 'USER_INVITATION_REVOKED_AT'),
            'valid_to' => Yii::t('app', 'USER_INVITATION_VALID_TO'),
            'token' => Yii::t('app', 'USER_INVITATION_TOKEN'),
            'message' => Yii::t('app', 'USER_INVITATION_MESSAGE'),
            'intended_role' => Yii::t('app', 'USER_INVITATION_ROLE'),
            'note' => Yii::t('app', 'USER_INVITATION_NOTE'),
            'language' => Yii::t('app', 'USER_INVITATION_LANGUAGE'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->valid_to = time() + 86400;
            $this->token = Yii::$app->security->generateRandomString() . '_' . time();
            $this->status = 'new';
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy():ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Sends an invitation to create an account
     * @return bool Success of the operation
     * @throws Exception
     */
    public function sendEmail()
    {
        $oldLanguage = Yii::$app->language;
        Yii::$app->language = $this->language;

        if (in_array($this->intended_role, User::operatorUserRoles())) {
            $baseUri = Yii::$app->params['uri.back'];
        } else {
            $baseUri = Yii::$app->params['uri.front'];
        }

        if (empty($baseUri)) {
            throw new Exception(Yii::t('app', 'USER_INVITATION_NO_BASE_URI'), 500);
        }

        $mail = Yii::$app->mailer
            ->compose(
                ['html' => 'invitation-html', 'text' => 'invitation-text'],
                [
                    'invitation' => $this,
                    'link' => $baseUri . Yii::$app->urlManager->createUrl(['user/accept', 'token' => $this->token]),
                ]
            )
            ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->name])
            ->setTo($this->email)
            ->setSubject(Yii::t('mail', 'INVITATION_EMAIL_SUBJECT'));

        $result = $mail->send();
        Yii::$app->language = $oldLanguage;
        return $result;
    }

    /**
     * Finds invitation by token
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByToken($token)
    {
        return static::findOne([
            'token' => $token,
        ]);
    }

    /**
     * Finds out if invitation is valid
     * @return bool
     */
    public function isInvitationValid():bool
    {
        return $this->valid_to >= time();
    }

    /**
     * Finds out if invitation has not been used
     * @return bool
     */
    public function isInvitationUnused():bool
    {
        return !$this->used_at;
    }

    /**
     * Finds out if invitation has not been revoked
     * @return bool
     */
    public function isInvitationUnRevoked():bool
    {
        return !$this->revoked_at;
    }

    /**
     * Finds out if invitation is still active
     * @return bool
     */
    public function isInvitationActive():bool
    {
        return $this->isInvitationValid() && $this->isInvitationUnused() && $this->isInvitationUnRevoked();
    }

    /**
     * @return bool
     */
    public function markAsOpened():bool
    {
        $this->opened_at = time();
        return $this->save();
    }

    /**
     * @return bool
     */
    public function markAsRevoked():bool
    {
        $this->revoked_at = time();
        return $this->save();
    }

    /**
     * @return bool
     */
    public function markAsUsed():bool
    {
        $this->used_at = time();
        return $this->save();
    }

    public function getIntendedRoleName():string
    {
        $names = User::userRoleNames();
        $code = $this->intended_role;
        return isset($names[$code]) ? $names[$code] : '?';
    }
}
