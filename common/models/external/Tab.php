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

    static public function createFromData($data): ExternalComponent
    {
        /* @todo Make it work with non-table boxes */
        $boxes = [];

        foreach ($data['data'] as $row) {
            $boxes[] = Box::createFromData($row);
        }

        $object = new Tab();

        $object->title = $data['title'] ?? '';
        $object->description = $data['description'] ?? '';
        $object->boxes = $boxes;

        return $object;
    }

    public function getContent(): string
    {
        $boxes = [];

        foreach ($this->boxes as $box) {
            $boxes[] = $box->getContent();
        }

        return '<p>' . $this->description . '</p>' . PHP_EOL . implode(PHP_EOL, $boxes);
    }
}
