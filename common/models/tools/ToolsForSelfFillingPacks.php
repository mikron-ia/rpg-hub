<?php

namespace common\models\tools;

use common\models\core\IsSelfFillingPack;
use common\models\Epic;

trait ToolsForSelfFillingPacks
{
    private function createAbsentRecords(Epic $epic, IsSelfFillingPack $pack, array $objectsRaw): bool
    {
        $users = $epic->participants;
        $objectsOrdered = [];

        foreach ($objectsRaw as $object) {
            $objectsOrdered[$object->user_id] = $object;
        }

        $result = true;

        foreach ($users as $user) {
            if (!isset($objectsOrdered[$user->user_id])) {
                $newObject = $pack->createEmptyContent($user->user_id);
                $saveResult = $newObject->save();
                $result = $result && $saveResult;
            }
        }
        return $result;
    }
}
