<?php


namespace console\controllers;


use common\models\User;
use common\models\user\PasswordChange;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

/**
 * Contain actions needed at the installation stage that do not fit into other controllers
 *
 * @package console\controllers
 */
class InstallController extends Controller
{
    /**
     * Adds an admin user to an empty database
     */
    public function actionAddAdministrator(): void
    {
        if (User::find()->count() > 0) {
            echo 'Users already exist, refusing to create admin';
            exit(ExitCode::UNSPECIFIED_ERROR);
        }

        $user = new User();

        $password = PasswordChange::generatePassword(4);

        echo 'Admin user creation initiated; all following fields are mandatory' . PHP_EOL . PHP_EOL;

        $user->username = $this->input('username');
        $user->email = $this->input('email');
        $user->user_role = User::USER_ROLE_ADMINISTRATOR;
        $user->setPassword($password);
        $user->generateAuthKey();

        echo PHP_EOL;

        if (!$user->save()) {
            echo 'Unable to save: ' . implode(', ', $user->getErrorSummary(true));
            exit(ExitCode::CANTCREAT);
        }

        echo 'Admin user created successfully; password: ' . $password;

        exit(ExitCode::OK);
    }

    /**
     * Generates a simple password that would be accepted by the system
     */
    public function actionGeneratePasswordSimple(): void
    {
        echo 'Generated password: ' . PasswordChange::generatePassword(4);
    }

    /**
     * Generates a complex password that would be accepted by the system
     */
    public function actionGeneratePasswordComplex(): void
    {
        echo 'Generated password: ' . PasswordChange::generatePassword(4, 0, 0, 2, 2);
    }

    private function input(string $label): string
    {
        $input = BaseConsole::input('Enter ' . $label . ': ');

        if (empty($input)) {
            BaseConsole::error('The ' . $label . ' must not be empty' . PHP_EOL);
            exit(ExitCode::NOINPUT);
        }

        return $input;
    }
}
