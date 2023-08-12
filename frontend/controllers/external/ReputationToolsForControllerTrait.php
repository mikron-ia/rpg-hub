<?php

namespace frontend\controllers\external;

use common\models\external\HasReputations;
use common\models\external\Reputation;
use common\models\external\ReputationEvent;

trait ReputationToolsForControllerTrait
{
    private function prepareReputationList(HasReputations $model): array
    {
        $reputation = [];
        if ($model->external_data_pack_id) {
            $data = $model->externalDataPack->getExternalDataByCode('reputations');
            if (isset($data)) {
                $reputation = Reputation::createFromArray($data);
            }
        }
        return $reputation;
    }

    private function prepareReputationEventsList(HasReputations $model): array
    {
        $event = [];
        if ($model->external_data_pack_id) {
            $data = $model->externalDataPack->getExternalDataByCode('reputationEvents');
            if (isset($data)) {
                $event = ReputationEvent::createFromArray($data);
            }
        }

        return $event;
    }
}
