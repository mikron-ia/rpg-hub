<?php

namespace console\controllers;

use common\components\service\ConfigVerificationService;
use Yii;
use yii\console\Controller;

class CheckController extends Controller
{
    /**
     * Checks the configuration for errors
     */
    public function actionConfig(): void
    {
        // Importance configuration
        echo 'Checking importance configuration...';
        $faultyImportanceKeys = ConfigVerificationService::checkImportanceConfigKeys();
        echo ' ' . (
            empty($faultyImportanceKeys)
                ? 'valid'
                : sprintf('INVALID OR MISSING (%s)', implode(', ', $faultyImportanceKeys))
            ) . PHP_EOL;

        // Key generation
        echo 'Checking key generation patterns...';
        $faultyKeyGenerationVariables = ConfigVerificationService::checkForNumberPlaceholders(Yii::$app->params['keyGeneration']);

        echo ' ' . (
            empty($faultyKeyGenerationVariables)
                ? 'valid'
                : sprintf(' INVALID OR MISSING (%s)', implode(', ', $faultyKeyGenerationVariables))
            ) . PHP_EOL;
    }
}
