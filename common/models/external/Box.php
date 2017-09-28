<?php

namespace common\models\external;


use yii\base\Model;

class Box extends Model implements ExternalComponent
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

    /**
     * Creates object from array
     * @param array $array
     * @return ExternalComponent
     */
    static public function createFromArray(array $array): ExternalComponent
    {
        $object = new Box([
            'title' => $array['title'] ?? '',
            'content' => $array['description'] ?? '[empty]'
        ]);

        return $object;
    }

    public function __toString()
    {
        return '<div>' . $this->content . '</div>';
    }
}
