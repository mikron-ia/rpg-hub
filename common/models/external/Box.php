<?php

namespace common\models\external;

use yii\base\Model;

class Box extends Model implements ExternalComponent
{
    public string $title;

    public ExternalComponent $content;

    private string $sizeClass;

    public static function createFromData(array|string $data): ExternalComponent
    {
        $type = $data['type'] ?? 'text';
        $size = $data['size'] ?? 'medium';

        $content = match ($type) {
            'table' => Table::createFromData($data),
            default => Text::createFromData($data),
        };

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
