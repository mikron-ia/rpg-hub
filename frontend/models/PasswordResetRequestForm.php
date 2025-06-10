<?php

namespace frontend\models;

use common\models\core\UserStatus;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
final class PasswordResetRequestForm extends Model
{
    public $email;

    public function rules(): array
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            [
                'email',
                'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => UserStatus::Active->value],
                'message' => Yii::t('app', 'PASSWORD_RESET_NO_USER'),
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('app', 'PASSWORD_RESET_EMAIL')
        ];
    }


    /**
     * Sends an email with a link, for resetting the password.
     * @return boolean whether the email was send
     */
    public function sendEmail(): bool
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => UserStatus::Active->value,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
        }

        if (!$user->save()) {
            return false;
        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([\Yii::$app->params['senderEmail'] => \Yii::$app->name])
            ->setTo($this->email)
            ->setSubject(Yii::t('app', 'PASSWORD_RESET_SUBJECT {name}', ['name' => \Yii::$app->name]))
            ->send();
    }
}
