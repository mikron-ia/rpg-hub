<?php

namespace console\controllers;

use common\components\LoggingHelper;
use common\models\exceptions\InvalidBackendConfigurationException;
use common\models\ImportancePack;
use yii\console\Controller;
use yii\db\Exception;

/**
 * @package console\controllers
 */
class ImportanceController extends Controller
{
    /**
     * Orders recalculation of every importance pack flagged for recalculation
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
