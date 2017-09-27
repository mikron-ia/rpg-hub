<?php

namespace common\models\external;


use yii\base\Model;

class Box extends Model implements ExternalComponent
{
    /**
     * @var string
     */
    public $title;

    public $content;

    /**
     * Creates object from array
     * @param array $array
     * @return ExternalComponent
     */
    static public function createFromArray(array $array): ExternalComponent
    {
        $object = new Box([
            'title' => $title,
            'description' => $array['description'] ?? ''
        ]);

        return $object;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
    }
}
