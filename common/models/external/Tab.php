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
        $object = new TableRow([
            'title' => $array['title'] ?? '',
            'description' => $array['description'] ?? ''
        ]);

        return $object;
    }

    public function __toString()
    {
        return $this->description;
    }
}
