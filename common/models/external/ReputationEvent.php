<?php

namespace common\models\external;

use yii\base\Model;

/**
 * Class Reputation
 * @package common\models\external
 */
class ReputationEvent extends Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $code;

    /**
     * @var int
     */
    public $value;

    /**
     * @var array
     */
    public $event;

    /**
     * @var string
     */
    public $participation;

    /**
     * @param $rawArray
     * @return ReputationEvent[]
     */
    static public function createFromArray($rawArray)
    {
        if(!$rawArray) {
            return [];
        }

        $objects = [];

        foreach ($rawArray as $object) {
            $objects[] = new ReputationEvent($object);
        }

        return array_reverse($objects, true);
    }
}
