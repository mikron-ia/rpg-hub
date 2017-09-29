<?php

namespace common\models\external;

use yii\base\Model;

class Tab extends Model implements ExternalComponent
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var Box[]
     */
    public $boxes;

    static public function createFromArray(array $array): ExternalComponent
    {
        /* @todo Make it work with non-table boxes */
        $boxes = [];

        foreach ($array['data'] as $row) {
            $boxes[] = Box::createFromArray($row);
        }

        $object = new Tab();

        $object->title = $array['title'] ?? '';
        $object->description = $array['description'] ?? '';
        $object->boxes = $boxes;

        return $object;
    }

    public function getContent()
    {
        $boxes = [];

        foreach ($this->boxes as $box) {
            $boxes[] = $box->getContent();
        }

        return '<p>' . $this->description . '</p>' . PHP_EOL . implode(PHP_EOL, $boxes);
    }
}
