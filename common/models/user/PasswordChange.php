<?php

namespace common\models\user;

use kartik\password\StrengthValidator;
use Yii;
use yii\base\Model;

final class PasswordChange extends Model
{
    /**
     * @var string
     */
    public $password_old;

    /**
     * @var string
     */
    public $password_new;

    /**
     * @var string
     */
    public $password_again;

    /**
     * @var \common\models\User
     */
    private $user;

    public function __construct($config = [])
    {
        $this->user = Yii::$app->user->identity;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['password_old', 'password_new', 'password_again'], 'required'],
            ['password_old', 'validatePassword'],
            [
                'password_new',
                StrengthValidator::class,
                'min' => 8,
                'lower' => 2,
                'upper' => 1,
                'special' => 0,
                'hasEmail' => true,
                'hasUser' => true,
                'allowSpaces' => true,
                'usernameValue' => $this->user->username,
            ],
            ['password_again', 'required'],
            [
                'password_again',
                'compare',
                'compareAttribute' => 'password_new',
                'message' => Yii::t('app', 'PASSWORD_CHANGE_ERROR_PASSWORDS_DO_NOT_MATCH')
            ],
        ];
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels['password_old'] = Yii::t('app', 'LABEL_PASSWORD_OLD');
        $labels['password_new'] = Yii::t('app', 'LABEL_PASSWORD_NEW');
        $labels['password_again'] = Yii::t('app', 'LABEL_PASSWORD_REPEAT');

        return $labels;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->user || !$this->user->validatePassword($this->password_old)) {
                $this->addError($attribute, Yii::t('app', 'PASSWORD_CHANGE_ERROR_OLD_PASSWORD_INCORRECT'));
            }
        }
    }

    /**
     * Saves the password
     * @return bool Success of the operations
     */
    public function savePassword()
    {
        if (!$this->hasErrors()) {
            $this->user->setPassword($this->password_new);
            return $this->user->save();
        }
        return false;
    }
}
