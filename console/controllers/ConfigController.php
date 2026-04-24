<?php

namespace console\controllers;

use common\components\service\ConfigVerificationService;
use Yii;
use yii\console\Controller;

class ConfigController extends Controller
{
    private const int MESSAGE_MAX = 36;
    private const string MESSAGE_FILLER = '.';

    /**
     * Checks the configuration for errors
     */
    public function actionCheckEnv(): void
    {
        // API configuration
        echo str_pad('Checking URI configuration...', self::MESSAGE_MAX, self::MESSAGE_FILLER);
        echo (ConfigVerificationService::checkUriConfig(
                getenv('URI_BACK'),
                getenv('URI_FRONT')
            ) ? 'valid' : 'INVALID OR MISSING') . PHP_EOL;

        // API configuration
        echo str_pad('Checking API configuration...', self::MESSAGE_MAX, self::MESSAGE_FILLER);
        echo (ConfigVerificationService::checkApiConfig(
                getenv('AUTHENTICATION_SIMPLE_KEY')
            ) ? 'valid' : 'INVALID OR MISSING') . PHP_EOL;

        // Importance configuration
        echo str_pad('Checking importance configuration...', self::MESSAGE_MAX, self::MESSAGE_FILLER);
        $faultyImportanceKeys = ConfigVerificationService::checkImportanceConfigKeys(
            $this->getEnvironmentalValues(ConfigVerificationService::IMPORTANCE_CONFIG_KEYS)
        );
        echo (
            empty($faultyImportanceKeys)
                ? 'valid'
                : sprintf('INVALID OR MISSING (%s)', implode(', ', $faultyImportanceKeys))
            ) . PHP_EOL;

        // Key generation
        echo str_pad('Checking key generation patterns...', self::MESSAGE_MAX, self::MESSAGE_FILLER);
        $faultyKeyGenerationVariables = ConfigVerificationService::checkForNumberPlaceholders(Yii::$app->params['keyGeneration']);

        echo (
            empty($faultyKeyGenerationVariables)
                ? 'valid'
                : sprintf(' INVALID OR MISSING (%s)', implode(', ', $faultyKeyGenerationVariables))
            ) . PHP_EOL;

        //  Front formatting
        echo str_pad('Checking front configuration...', self::MESSAGE_MAX, self::MESSAGE_FILLER);
        $faultyFrontFormattingKeys = ConfigVerificationService::checkFrontFormattingValues(
            $this->getEnvironmentalValues(ConfigVerificationService::FRONT_FORMATTING_CONFIG_KEYS)
        );

        echo (
            empty($faultyFrontFormattingKeys)
                ? 'valid'
                : sprintf('INVALID OR MISSING (%s)', implode(', ', $faultyFrontFormattingKeys))
            ) . PHP_EOL;
    }

    private function getEnvironmentalValues(array $keys): array
    {
        $array_map = [];
        foreach ($keys as $key) {
            $array_map[$key] = getenv($key);
        }
        return $array_map;
    }
}
