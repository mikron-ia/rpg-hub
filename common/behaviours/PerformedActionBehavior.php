<?php

namespace common\behaviours;

use common\models\PerformedAction;
use Override;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;
use yii\db\Exception;

class PerformedActionBehavior extends Behavior
{
    /**
     * @var string Name of the ID field in the table
     */
    public string $idName = 'id';

    /**
     * @var string Class name to be used in records
     */
    public string $className = 'Unknown';

    /**
     * @return array<string,string>
     */
    #[Override]
    public function events(): array
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'createRecord',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'createRecord',
        ];
    }

    /**
     * This method looks unused to IDE because it is called via the framework - see events()
     *
     * @throws Exception
     */
    public function createRecord(Event $event): void
    {
        PerformedAction::createRecord(
            operation: match ($event->name) {
                BaseActiveRecord::EVENT_AFTER_INSERT => PerformedAction::PERFORMED_ACTION_CREATE,
                BaseActiveRecord::EVENT_AFTER_UPDATE => PerformedAction::PERFORMED_ACTION_UPDATE,
                default => PerformedAction::PERFORMED_ACTION_OTHER,
            },
            class: $this->className,
            object_id: $this->owner->{$this->idName}
        );
    }
}
