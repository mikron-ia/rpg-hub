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
     * @param array $array
     * @return ExternalComponent
     */
    static public function createFromArray(array $array): ExternalComponent
    {
        $type = $array['type'] ?? 'div';
        $size = $array['size'] ?? 'medium';

        if ($type === 'table') {
            $content = Table::createFromArray($array);
        } else {
            $content = '[empty]';
        }

        $object = new Box();

        $object->title = $array['title'] ?? '';
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

    public function getContent()
    {
        return '<div class="' . $this->sizeClass . '">' . $this->content->getContent() . '</div>';
    }
}
