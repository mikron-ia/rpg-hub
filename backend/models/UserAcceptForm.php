<?php
namespace backend\models;

use common\models\core\Language;
use common\models\User;
use kartik\password\StrengthValidator;
use Yii;
use yii\base\Model;

/**
 * Class CreateUserForm
 * @package backend\models
 */
class UserAcceptForm extends Model
{
    public $username;
    public $email;
    public $language;
    public $user_role;
    public $password;
    public $password_again;

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'USER_EMAIL'),
            'created_at' => Yii::t('app', 'USER_CREATED_AT'),
            'updated_at' => Yii::t('app', 'USER_UPDATED_AT'),
            'language' => Yii::t('app', 'USER_LANGUAGE'),
            'status' => Yii::t('app', 'USER_STATUS'),
            'user_role' => Yii::t('app', 'USER_ROLE'),
        ];
    }

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
            ['language', 'in', 'range' => Language::supportedLanguages()],
            ['password', 'validatePassword'],
            [
                'password_new',
                StrengthValidator::className(),
                'min' => 8,
                'lower' => 2,
                'upper' => 1,
                'special' => 0,
                'hasEmail' => true,
                'hasUser' => true,
                'usernameValue' => $this->username,
            ],
            ['password_again', 'required'],
            [
                'password_again',
                'compare',
                'compareAttribute' => 'password',
                'message' => Yii::t('app', 'PASSWORD_CHANGE_ERROR_PASSWORDS_DO_NOT_MATCH')
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
     * @todo Add rights
     */
    private function setUser()
    {
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generatePasswordResetToken();

        return $user->save() ? $user : null;
    }
}
