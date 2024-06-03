<?php

namespace console\controllers;

use common\components\LoggingHelper;
use common\models\ImportancePack;
use yii\console\Controller;
use yii\db\Exception;

/**
 * Class ImportanceController
 *
 * @package console\controllers
 */
class ImportanceController extends Controller
{
    /**
     * Orders recalculation of every importance pack flagged for recalculation
     */
    public function actionRecalculate(): void
    {
        if ($this->recalculate(true)) {
            LoggingHelper::log('Conditional recalculation completed', 'importance.calculator.summary');
        } else {
            LoggingHelper::log('Conditional recalculation not completed', 'importance.calculator.summary');
        }
    }

    /**
     * Orders recalculation of every importance pack regardless of flagging
     */
    public function actionRecalculateUnconditionally(): void
    {
        if ($this->recalculate(false)) {
            LoggingHelper::log('Unconditional recalculation completed', 'importance.calculator.summary');
        } else {
            LoggingHelper::log('Unconditional recalculation not completed', 'importance.calculator.summary');
        }
    }

    /**
     * Recalculates packs
     *
     * @param bool $considerFlag Whether to consider recalculation flags
     *
     * @return bool
     *
     * @throws Exception
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
                    'Pack ' . $pack->importance_pack_id . ' failed',
                    'importance.calculator.process'
                );
            }
        }

        LoggingHelper::log(
            'Attempted ' . count($packs) . ' recalculations, succeeded with ' . $successful . '.',
            'importance.calculator.process'
        );

        return ($successful === count($packs));
    }
}
