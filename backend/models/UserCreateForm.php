<?php

namespace backend\models;

use common\models\core\Language;
use common\models\core\UserStatus;
use common\models\User;
use common\models\UserInvitation;
use Override;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @package backend\models
 */
final class UserCreateForm extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $language;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $note;

    /**
     * @var string
     */
    public $user_role;

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('app', 'USER_INVITATION_EMAIL'),
            'message' => Yii::t('app', 'USER_INVITATION_MESSAGE'),
            'user_role' => Yii::t('app', 'USER_INVITATION_ROLE'),
            'note' => Yii::t('app', 'USER_INVITATION_NOTE'),
            'language' => Yii::t('app', 'USER_INVITATION_LANGUAGE'),
        ];
    }

    #[Override]
    public function attributeHints(): array
    {
        return [
            'message' => Yii::t('app', 'USER_INVITATION_HINT_MESSAGE'),
            'note' => Yii::t('app', 'USER_INVITATION_HINT_NOTE'),
        ];
    }

    #[Override]
    public function rules(): array
    {
        return [
            [['email', 'message', 'user_role'], 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
                'email',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('app', 'USER_CREATION_EMAIL_TAKEN'),
                'filter' => ['status' => UserStatus::Active->value],
            ],
            ['language', 'in', 'range' => Language::supportedLanguages()],
            [['note'], 'string', 'max' => 255],
            ['user_role', 'in', 'range' => User::allowedUserRoles()],
        ];
    }

    /**
     * @throws Exception
     */
    public function createUserInvitation(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $invitation = new UserInvitation();

        $invitation->email = $this->email;
        $invitation->intended_role = $this->user_role;
        $invitation->language = $this->language;
        $invitation->message = $this->message;
        $invitation->note = $this->note;

        return $invitation->save();
    }
}
