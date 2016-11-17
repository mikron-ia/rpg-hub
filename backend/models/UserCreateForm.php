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
    public $user_role;

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'USER_EMAIL'),
            'language' => Yii::t('app', 'USER_LANGUAGE'),
            'user_role' => Yii::t('app', 'USER_ROLE'),
        ];
    }

    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
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
            ['user_role', 'in', 'range' => User::allowedUserRoles()]
        ];
    }

    /**
     * Signs user up
     * @return bool
     */
    public function signUp()
    {
        if (!$this->validate()) {
            return null;
        }

        $invitation = new UserInvitation();

        $invitation->email = $this->email;
        $invitation->language = $this->language;
        $invitation->intended_role = $this->user_role;
        $invitation->message = "Welcome";

        return $invitation->save();
    }

    /**
     * Stop-gap method that informs that this is a creation
     * @return bool
     */
    public function getIsNewRecord()
    {
        return true;
    }
}
