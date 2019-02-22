<?php

namespace common\models\external;

use yii\base\Model;

class Text extends Model implements ExternalComponent
{
    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $title = '';

    /**
     * @var string
     */
    public $text;

    /**
     * Creates object from array
     * @param array $data
     * @return ExternalComponent
     */
    static public function createFromData($data): ExternalComponent
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
