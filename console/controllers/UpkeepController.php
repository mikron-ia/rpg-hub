<?php

namespace console\controllers;

use yii\console\Controller;

/**
 * Collects cron-based actions
 *
 * Those actions are intended to be run by cron calls, without much of the user input. Their purpose it to maintain
 * the system - keeping the data updated, incorporate new objects into full operational capability, recognising new
 * users and configuring their assets. All tasks included here do not have to be performed immediately - ideally, this
 * should only call up workers that perform the tasks.
 */
class UpkeepController extends Controller
{
    /**
     * Runs things that should be run often and are cheap
     *
     * The suggested frequency for this action is every hour.
     */
    public function actionRunsOftenIsCheap(): void
    {
        /** @todo Automated e-mail sending from the queue */
        exit(0);
    }

    /**
     * Runs things that should be run often, but are not very cheap
     *
     * The suggested frequency for this action is every day.
     *
     * If in doubt whether action is expensive or cheap, assume it is expensive.
     */
    public function actionRunsOftenIsExpensive(): void
    {


        exit(0);
    }

    /**
     * Runs things that should be run rarely, but are relatively cheap
     *
     * The suggested frequency for this action is every week.
     *
     * Keep in mind that actions grouped here could be considered expensive if run often. If it is possible, this action
     * should be run outside business hours.
     */
    public function actionRunsRarelyIsCheap(): void
    {
        exit(0);
    }

    /**
     * Runs things that should be run rarely and are expensive
     *
     * The suggested frequency for this action is every month.
     *
     * Those actions put notable strain on the server and should be run only at times of low activity - preferably
     * outside business hours.
     */
    public function actionRunsRarelyIsExpensive(): void
    {
        /* @todo Database diagnostic and integrity checks */
        exit(0);
    }
}
