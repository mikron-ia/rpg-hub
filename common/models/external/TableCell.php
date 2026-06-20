<?php

namespace common\models\external;

use yii\base\Model;

class TableCell extends Model implements ExternalComponent
{
    public string $class;

    public string $content;

    public string $tag;

    public ?string $title;

    public static function createFromData(array|string $data): ExternalComponent
    {
        $object = new TableCell();

        $classes = [];

        if (isset($data['class'])) {
            $classList = explode(' ', $data['class']);
            foreach ($classList as $class) {
                $classes[] = 'external-data-' . $class;
            }
        }

        $object->class = implode(' ', $classes);
        $object->content = $data['data'] ?? '';
        $object->tag = $data['tag'] ?? 'td';
        $object->title = $data['title'] ?? null;

        return $object;
    }

    public function getContent(): string
    {
        $title = $this->title ? (' title="' . $this->title . '"') : '';
        $class = $this->class ? (' class="' . $this->class . '"') : '';
        return '<' . $this->tag . $title . $class . '>' . $this->content . '</' . $this->tag . '>';
    }
}
