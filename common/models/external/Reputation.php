<?php

namespace common\models\external;

use yii\base\Model;

/**
 * Class Reputation
 * @package common\models\external
 */
class Reputation extends Model
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
     * @var string
     */
    public $description;

    /**
     * @var int[]
     */
    public $value;

    /**
     * @param $reputationRawArray
     * @return Reputation[]
     */
    static public function createFromArray($reputationRawArray)
    {
        if(!$reputationRawArray) {
            return [];
        }

        $reputations = [];

        foreach ($reputationRawArray as $reputation) {
            $reputations[] = new Reputation($reputation);
        }

        return $reputations;
    }
}
