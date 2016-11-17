<?php
namespace backend\models;

use common\models\core\Language;
use common\models\User;
use common\models\UserInvitation;
use Yii;
use yii\base\Model;

/**
 * Class CreateUserForm
 * @package backend\models
 */
class UserCreateForm extends Model
{
    public $email;
    public $language;
    public $message;
    public $note;
    public $user_role;

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'USER_INVITATION_EMAIL'),
            'message' => Yii::t('app', 'USER_INVITATION_MESSAGE'),
            'user_role' => Yii::t('app', 'USER_INVITATION_ROLE'),
            'note' => Yii::t('app', 'USER_INVITATION_NOTE'),
            'language' => Yii::t('app', 'USER_INVITATION_LANGUAGE'),
        ];
    }

    public function rules()
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
                'filter' => ['status' => User::STATUS_ACTIVE],
            ],
            ['language', 'in', 'range' => Language::supportedLanguages()],
            [['note'], 'string', 'max' => 255],
            ['user_role', 'in', 'range' => User::allowedUserRoles()]
        ];
    }

    /**
     * Signs user up
     * @return bool
     */
    public function signUp():bool
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
