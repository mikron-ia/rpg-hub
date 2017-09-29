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
     * @var ExternalComponent
     */
    public $content;

    /**
     * Creates object from array
     * @param array $array
     * @return ExternalComponent
     */
    static public function createFromArray(array $array): ExternalComponent
    {
        $type = $array['type'] ?? 'div';

        if ($type === 'table') {
            $content = Table::createFromArray($array);
        } else {
            $content = '[empty]';
        }

        $object = new Box();

        $object->title = $array['title'] ?? '';
        $object->content = $content;

        return $object;
    }

    public function getContent()
    {
        return '<div class="col-md-4">' . $this->content->getContent() . '</div>';
    }
}
