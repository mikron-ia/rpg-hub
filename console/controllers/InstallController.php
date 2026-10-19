<?php

namespace console\controllers;

use common\models\User;
use common\models\user\PasswordChange;
use yii\base\Exception;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

/**
 * Contain actions needed at the installation stage
 */
class InstallController extends Controller
{
    /**
     * Adds an admin user to an empty database
     *
     * @throws Exception
     */
    public function actionAddAdministrator(): void
    {
        if (User::find()->count() > 0) {
            $this->error('Users already exist, refusing to create admin');
            exit(ExitCode::UNSPECIFIED_ERROR);
        }

        $user = new User();

        $password = PasswordChange::generatePassword(4);

        $this->output('Admin user creation initiated; all following fields are mandatory');

        $user->username = $this->input('username');
        $user->email = $this->input('email');
        $user->user_role = User::USER_ROLE_ADMINISTRATOR;
        $user->setPassword($password);
        $user->generateAuthKey();

        if (!$user->save()) {
            $this->error('Unable to save: ' . implode(', ', $user->getErrorSummary(true)));
            exit(ExitCode::CANTCREAT);
        }

        $this->output('Admin user created successfully; password: ' . $password);

        exit(ExitCode::OK);
    }

    /**
     * Generates a simple password that would be accepted by the system
     */
    public function actionGeneratePasswordSimple(): void
    {
        $this->output('Generated password: ' . PasswordChange::generatePassword(4));
    }

    /**
     * Generates a complex password that would be accepted by the system
     */
    public function actionGeneratePasswordComplex(): void
    {
        $this->output('Generated password: ' . PasswordChange::generatePassword(4, 0, 0, 2, 2));
    }

    private function error(string $message): void
    {
        BaseConsole::error(PHP_EOL . $message . PHP_EOL);
    }

    private function input(string $label): string
    {
        $input = BaseConsole::input('Enter ' . $label . ': ');

        if (empty($input)) {
            $this->error('The ' . $label . ' must not be empty');
            exit(ExitCode::NOINPUT);
        }

        return $input;
    }

    private function output(string $message): void
    {
        BaseConsole::output(PHP_EOL . $message . PHP_EOL);
    }
}
