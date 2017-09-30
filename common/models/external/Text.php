<?php

namespace common\models\external;

use yii\base\Model;

class Text extends Model implements ExternalComponent
{
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

        $object->text = $data['text'] ?? '<p>' . \Yii::t('app', 'EXTERNAL_COMPONENT_NO_DATA') . '</p>';
        $object->title = $data['title'] ?? '';

        return $object;
    }

    public function getContent(): string
    {
        return '<h3 class="center">' . $this->title . '</h3><div>' . $this->text . '</div>' . PHP_EOL;
    }
}
