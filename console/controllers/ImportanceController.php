<?php

namespace console\controllers;

use common\components\LoggingHelper;
use common\models\exceptions\InvalidBackendConfigurationException;
use common\models\ImportancePack;
use yii\console\Controller;
use yii\db\Exception;

/**
 * Recalculates importance values for objects with `HasImportance` and `ImportancePack`
 */
class ImportanceController extends Controller
{
    /**
     * Orders recalculation of every importance pack flagged for recalculation
     *
     * This action recalculates the importance values only for objects that have been flagged, which usually means
     * a small percentage of the entire database. It is thus intended to be called often; an interval of an hour or
     * half an hour is recommended to keep the objects properly ordered based on importance.
     *
     * @throws Exception
     * @throws InvalidBackendConfigurationException
     */
    public function actionRecalculate(): void
    {
        LoggingHelper::log('Conditional recalculation started', 'importance.calculator.state');
        LoggingHelper::log(
            message: $this->recalculate(true)
                ? 'Conditional recalculation completed'
                : 'Conditional recalculation not completed',
            prefix: 'importance.calculator.state'
        );
    }

    /**
     * Orders recalculation of every importance pack regardless of flagging
     *
     * This action recalculates the importance values for all objects that have it, which means its runtime grows
     * linearly with the database. Its purpose is two-fold: to catch cases not flagged for any reason but still
     * deserving recalculation and to apply new importance-related configuration values. Since both cases are rare,
     * it is recommended to either set this action to run once a week at most, or to run it manually when needed.
     *
     * @throws Exception
     * @throws InvalidBackendConfigurationException
     */
    public function actionRecalculateUnconditionally(): void
    {
        LoggingHelper::log('Unconditional recalculation started', 'importance.calculator.state');
        LoggingHelper::log(
            message: $this->recalculate(false)
                ? 'Conditional recalculation completed'
                : 'Conditional recalculation not completed',
            prefix: 'importance.calculator.state'
        );
    }

    /**
     * Recalculates packs
     *
     * @throws Exception
     * @throws InvalidBackendConfigurationException
     */
    private function recalculate(bool $considerFlag): bool
    {
        $query = ImportancePack::find();

        if ($considerFlag) {
            $query->where(['flagged' => true]);
        }

        $packs = $query->all();

        $successful = 0;

        foreach ($packs as $pack) {
            /** @var $pack ImportancePack */
            if ($pack->recalculatePack()) {
                $successful++;
                $pack->unflagForRecalculation();
            } else {
                LoggingHelper::log(
                    message: sprintf('Pack %d failed', $pack->importance_pack_id),
                    prefix: 'importance.calculator.process'
                );
            }
        }

        LoggingHelper::log(
            message: sprintf('Attempted %d recalculations, succeeded with %d.', count($packs), $successful),
            prefix: 'importance.calculator.summary'
        );

        return ($successful === count($packs));
    }
}
