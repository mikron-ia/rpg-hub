<?php

namespace console\controllers;

use common\components\LoggingHelper;
use common\models\ImportancePack;
use yii\console\Controller;

/**
 * Class ImportanceController
 * @package console\controllers
 */
class ImportanceController extends Controller
{
    /**
     * Orders recalculation of every importance pack flagged for recalculation
     */
    public function actionRecalculate()
    {
        if ($this->recalculate(true)) {
            LoggingHelper::log("Conditional recalculation completed", 'importance.calculator.summary');
        } else {
            LoggingHelper::log("Conditional recalculation not completed", 'importance.calculator.summary');
        }
    }

    /**
     * Orders recalculation of every importance pack regardless of flagging
     */
    public function actionRecalculateUnconditionally()
    {
        if ($this->recalculate(false)) {
            LoggingHelper::log("Unconditional recalculation completed", 'importance.calculator.summary');
        } else {
            LoggingHelper::log("Unconditional recalculation not completed", 'importance.calculator.summary');
        }
    }

    /**
     * Recalculates packs
     *
     * @param bool $considerFlag Whether to consider recalculation flags
     * @return bool
     */
    private function recalculate(bool $considerFlag): bool
    {
        $query = ImportancePack::find();

        if ($considerFlag) {
            $query->where('1=1'); // @todo Add condition on flag once flag exists
        }

        $packs = $query->all();

        $successful = 0;

        foreach ($packs as $pack) {
            /** @var $pack ImportancePack */
            if ($pack->recalculatePack()) {
                $successful++;
            } else {
                LoggingHelper::log(
                    "Pack " . $pack->importance_pack_id . " failed",
                    'importance.calculator.process'
                );
            }
        }

        LoggingHelper::log(
            "Attempted " . count($packs) . " recalculations, succeeded with " . $successful . ".",
            'importance.calculator.process'
        );

        return ($successful === count($packs));
    }
}
