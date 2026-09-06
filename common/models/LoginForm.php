<?php

namespace common\models;

use Override;
use Yii;
use yii\base\Model;
use yii\db\Exception as DbException;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    private const int REMEMBER_TIME_IN_SECONDS = 2592000;

    #[Override]
    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    #[Override]
    public function attributeLabels(): array
    {
        return [
            'username' => Yii::t('app', 'LOGIN_USERNAME'),
            'password' => Yii::t('app', 'LOGIN_PASSWORD'),
            'rememberMe' => Yii::t('app', 'LOGIN_REMEMBER'),
        ];
    }

    #[Override]
    public function attributeHints(): array
    {
        return [
            'username' => Yii::t('app', 'LOGIN_HINT_USERNAME'),
        ];
    }

    /**
     * Validates the password
     *
     * This method serves as the inline validation for password
     *
     * @param string $attribute the attribute currently being validated
     * @param array|null $params the additional name-value pairs given in the rule
     */
    public function validatePassword(string $attribute, ?array $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password
     *
     * @return boolean whether the user is logged in successfully
     *
     * @throws DbException
     */
    public function login(): bool
    {
        if ($this->validate()) {
            $result = Yii::$app->user->login($this->getUser(), $this->rememberMe ? self::REMEMBER_TIME_IN_SECONDS : 0);

            if ($result) {
                PerformedAction::createSimplifiedRecord(PerformedAction::PERFORMED_ACTION_LOGIN);
            }

            return $result;
        }

        return false;
    }

    protected function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
