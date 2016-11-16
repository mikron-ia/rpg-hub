<?php
namespace backend\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Class CreateUserForm
 * @package backend\models
 */
class CreateUserForm extends Model
{
    public $username;
    public $email;
    public $password;

    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            [
                'username',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('app', 'USER_CREATION_USERNAME_TAKEN'),
                'filter' => ['status' => User::STATUS_ACTIVE],
            ],
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
        ];
    }

    /**
     * Signs user up
     * @return User|null the saved model or null if saving fails
     */
    public function signUp()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = $this->setUser();

        if ($user) {
            if(!$user->language) {
                $user->language = Yii::$app->language;
            }
        }

        return $user;
    }

    /**
     * Creates user object with data from form and generates keys & passwords
     * @return User|null User object on success, null on failure
     */
    private function setUser()
    {
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword(uniqid("new-user-", true));
        $user->generateAuthKey();
        $user->generatePasswordResetToken();

        return $user->save() ? $user : null;
    }
}
