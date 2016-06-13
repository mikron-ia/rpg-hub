<?php

namespace common\components;

use common\models\User;
use yii\base\BootstrapInterface;

class LanguageSelector implements BootstrapInterface
{
    public $supportedLanguages = [];

    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        if (empty($preferredLanguage)) {
            if (!$app->user->isGuest) {
                /** @var User|null $user */
                $user = $app->user->identity;
                $preferredLanguage = isset($user)?$user->language:null;
            }
        }

        /* If not found - most likely due to not being logged in - get the settings from preferred languages */
        if (empty($preferredLanguage)) {
            $preferredLanguage = $app->request->getPreferredLanguage($this->supportedLanguages);
        }

        $app->language = $preferredLanguage;
    }
}
