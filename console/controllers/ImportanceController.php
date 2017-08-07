<?php

namespace console\controllers;

/**
 * Class ImportanceController
 * @package console\controllers
 */
class ImportanceController
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
        return true;
    }
}
