<?php

namespace common\behaviours;

use common\models\PerformedAction;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;

class PerformedActionBehavior extends Behavior
{
    /**
     * @var string Name of the ID field in the table
     */
    public $idName = 'id';

    /**
     * @var string Class name to be used in records
     */
    public $className = 'Unknown';

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'createRecord',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'createRecord',
        ];
    }

    /**
     * @param Event $event
     */
    public function createRecord(Event $event)
    {
        $idName = $this->idName;
        $id = $this->owner->$idName;

        switch ($event->name) {
            case BaseActiveRecord::EVENT_AFTER_INSERT :
                $action = PerformedAction::PERFORMED_ACTION_CREATE;
                break;
            case BaseActiveRecord::EVENT_AFTER_UPDATE :
                $action = PerformedAction::PERFORMED_ACTION_UPDATE;
                break;
            default :
                $action = PerformedAction::PERFORMED_ACTION_OTHER;
                break;
        }

        PerformedAction::createRecord($action, $this->className, $id);
    }
}
