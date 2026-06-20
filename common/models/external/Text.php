<?php

namespace common\models\external;

use yii\base\Model;

class Text extends Model implements ExternalComponent
{
    public string $class;

    public string $title = '';

    public string $text;

    public static function createFromData(array|string $data): ExternalComponent
    {
        $object = new Text();

        $classes = [];

        if (isset($data['class'])) {
            $classList = explode(' ', $data['class']);
            foreach ($classList as $class) {
                $classes[] = 'external-data-' . $class;
            }
        }

        $object->class = implode(' ', $classes);
        $object->text = $data['text'] ?? '<p>' . \Yii::t('app', 'EXTERNAL_COMPONENT_NO_DATA') . '</p>';
        $object->title = $data['title'] ?? '';

        return $object;
    }

    public function getContent(): string
    {
        $class = $this->class ? (' class="' . $this->class . '"') : '';
        return '<h3 class="center">' . $this->title . '</h3><div' . $class . '>' . $this->text . '</div>' . PHP_EOL;
    }
}
