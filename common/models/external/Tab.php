<?php

namespace common\models\external;

use common\components\processor\LinkTagsProcessor;
use common\components\processor\MarkdownProcessor;
use Yii;
use yii\base\Model;

class Tab extends Model implements ExternalComponent
{
    public string $title;

    public string $description;

    /**
     * @var Box[]
     */
    public array $boxes;

    public static function createFromData(array|string $data): ExternalComponent
    {
        $boxes = [];

        foreach ($data['data'] ?? [] as $row) {
            $boxes[] = Box::createFromData($row);
        }

        $object = new Tab();

        $object->title = $data['title'] ?? Yii::t('external', 'CHARACTER_SHEET_TAB_TITLE_NEEDED');
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

    public function getContentWithLinks(): string
    {
        return MarkdownProcessor::findAndFixLinksWithMarkdown(LinkTagsProcessor::processKeys($this->getContent()));
    }
}
