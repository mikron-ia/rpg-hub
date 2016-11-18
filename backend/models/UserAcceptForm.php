<?php
namespace backend\models;

use common\models\core\Language;
use common\models\User;
use common\models\UserInvitation;
use kartik\password\StrengthValidator;
use Yii;
use yii\base\InvalidParamException;
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

    /**
     * @var UserInvitation
     */
    private $invitation;

    public function __construct(string $token, array $config = [])
    {
        $this->invitation = UserInvitation::findByToken($token);

        if (!$this->invitation) {
            throw new InvalidParamException(Yii::t('app', 'USER_INVITATION_NOT_FOUND'));
        }

        $this->email = $this->invitation->email;
        parent::__construct($config);
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'USER_EMAIL'),
            'created_at' => Yii::t('app', 'USER_CREATED_AT'),
            'updated_at' => Yii::t('app', 'USER_UPDATED_AT'),
            'language' => Yii::t('app', 'USER_LANGUAGE'),
            'status' => Yii::t('app', 'USER_STATUS'),
            'user_role' => Yii::t('app', 'USER_ROLE'),
            'username' => Yii::t('app', 'USER_USERNAME'),
            'password' => Yii::t('app', 'USER_PASSWORD'),
            'password_again' => Yii::t('app', 'USER_PASSWORD_REPEATED'),
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
            ['password', 'required'],
            [
                'password',
                StrengthValidator::className(),
                'min' => 8,
                'lower' => 2,
                'upper' => 1,
                'special' => 0,
                'hasEmail' => true,
                'hasUser' => true,
                'allowSpaces' => true,
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
            if (!$user->language) {
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
        $user->user_role = $this->invitation->intended_role;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
