<?php

namespace console\controllers;

use common\models\ImportancePack;
use yii\console\Controller;

/**
 * Class ImportanceController
 * @package console\controllers
 */
class ImportanceController extends Controller
{
    public function actionRecalculate()
    {
        if ($this->recalculate(true)) {
            exit(0);
        } else {
            exit("Recalculation not completed");
        }
    }

    public function actionRecalculateUnconditionally()
    {
        if ($this->recalculate(false)) {
            exit(0);
        } else {
            exit("Recalculation not completed");
        }
    }

    private function recalculate(bool $considerFlag):bool
    {
        $query = ImportancePack::find();

        if ($considerFlag) {
            $query->where('1=1'); // @todo Add condition on flag once flag exists
        }

        $packs = $query->all();

        $successful = 0;

        foreach ($packs as $pack) {
            /** @var $pack ImportancePack */
            if ($pack->recalculatePacks()) {
                $successful++;
            } else {
                echo "Pack " . $pack->importance_pack_id . " failed" . PHP_EOL . PHP_EOL;
            }
        }

        echo "Attempted " . count($packs) . " recalculations, succeeded with " . $successful . "." . PHP_EOL . PHP_EOL;

        return ($successful === count($packs));
    }
}
