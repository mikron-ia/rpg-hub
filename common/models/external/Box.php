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
     * @var string Class for the encompassing div
     */
    private $sizeClass;

    /**
     * Creates object from array
     * @param array $data
     * @return ExternalComponent
     */
    static public function createFromData($data): ExternalComponent
    {
        $type = $data['type'] ?? 'text';
        $size = $data['size'] ?? 'medium';

        switch ($type) {
            case 'table' :
                $content = Table::createFromData($data);
                break;
            case 'text' :
                $content = Text::createFromData($data);
                break;
            default :
                $content = Text::createFromData($data);
                break;

        }

        $object = new Box();

        $object->title = $data['title'] ?? '';
        $object->content = $content;

        switch ($size) {
            case 'smallest' :
                $object->sizeClass = 'col-md-2';
                break;
            case 'smaller' :
                $object->sizeClass = 'col-md-3';
                break;
            case 'small' :
                $object->sizeClass = 'col-md-4';
                break;
            case 'medium' :
                $object->sizeClass = 'col-md-6';
                break;
            case 'large' :
                $object->sizeClass = 'col-md-8';
                break;
            case 'larger' :
                $object->sizeClass = 'col-md-10';
                break;
            case 'largest' :
                $object->sizeClass = 'col-md-12';
                break;
            default:
                $numericalSize = (int)$size;
                if ($numericalSize > 0 && $numericalSize <= 12) {
                    $object->sizeClass = 'col-md-' . $numericalSize;
                } else {
                    $object->sizeClass = 'col-md-6';
                }
        }

        return $object;
    }

    public function getContent(): string
    {
        return '<div class="' . $this->sizeClass . '">' . $this->content->getContent() . '</div>';
    }
}
