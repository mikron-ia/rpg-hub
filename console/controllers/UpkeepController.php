<?php

namespace console\controllers;

use yii\console\Controller;

/**
 * Class UpkeepController
 *
 * Those actions are intended to be run by cron calls, without much of the user input. Their purpose it to maintain
 * the system - keeping the data updated, incorporate new objects into full operational capability, recognising new
 * users and configuring their assets. All tasks included here do not have to be performed immediately - ideally, this
 * should only call up workers that perform the tasks.
 *
 * @package console\controllers
 */
class UpkeepController extends Controller
{
    /**
     * Things that should be run often and are cheap
     * Suggested frequency for this action is every hour.
     */
    public function actionRunsOftenIsCheap()
    {
        /** @todo Automated e-mail sending from the queue */
        exit(0);
    }

    /**
     * Things that should be run often, but are not very cheap
     * If in doubt whether action is expensive or cheap, assume it is expensive
     * Suggested frequency for this action is every day.
     */
    public function actionRunsOftenIsExpensive()
    {


        exit(0);
    }

    /**
     * Things that should be run rarely, but are relatively cheap
     * Keep in mind that actions grouped here could be considered expensive if run often. If it is possible, this action
     * should be run outside business hours.
     * Suggested frequency for this action is every week.
     */
    public function actionRunsRarelyIsCheap()
    {
        exit(0);
    }

    /**
     * Things that should be run rarely and are expensive
     * Those actions put notable strain on the server and should be run only at times of low activity - preferably
     * outside business hours.
     * Suggested frequency for this action is every month.
     */
    public function actionRunsRarelyIsExpensive()
    {
        /* @todo Database diagnostic and integrity checks */
        exit(0);
    }
}
