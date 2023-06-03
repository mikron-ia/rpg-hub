<?php

namespace common\models\user;

use kartik\password\StrengthValidator;
use Yii;
use yii\base\Model;

final class PasswordChange extends Model
{
    private const MINIMAL_SIZE = 8;

    private const LOWER = 2;
    private const UPPER = 1;
    private const NUMBER = 0;
    private const SPECIAL = 0;

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
                'min' => self::MINIMAL_SIZE,
                'lower' => self::LOWER,
                'upper' => self::UPPER,
                'special' => self::SPECIAL,
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

    /*
     * Generates a password that would be accepted by the system
     */
    public static function generatePassword(
        int $multiplier = 1,
        int $addedSmalls = 0,
        int $addedBigs = 0,
        int $addedSpecials = 0,
        int $addedNumbers = 0
    ): string {
        $numbers = '0123456789';
        $smalls = 'abcdefghijklmnopqrstuvwxyz';
        $bigs = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $specials = '_=+-.,/?!@#$%^&*()';

        $password = '';

        do {
            for ($i = -$addedSmalls; $i < self::LOWER; $i++) {
                $password .= $smalls[rand(0, 25)];
            }

            for ($i = -$addedBigs; $i < self::UPPER; $i++) {
                $password .= $bigs[rand(0, 25)];
            }

            for ($i = -$addedSpecials; $i < self::SPECIAL; $i++) {
                $password .= $specials[rand(0, 17)];
            }

            for ($i = -$addedNumbers; $i < self::NUMBER; $i++) {
                $password .= $numbers[rand(0, 9)];
            }
        } while (strlen($password) < self::MINIMAL_SIZE * $multiplier);

        $array = str_split($password);
        shuffle($array);
        return implode($array);
    }
}
