<?php

namespace common\models\external;

use yii\base\Model;

class Table extends Model implements ExternalComponent
{
    public string $caption = '';

    /**
     * @var TableRow[]
     */
    public array $rows;

    public static function createFromData(array|string $data): ExternalComponent
    {
        $object = new Table();

        $object->rows = [];
        foreach ($data['rows'] ?? [] as $record) {
            $object->rows[] = TableRow::createFromData($record);
        }

        $object->caption = $data['title'] ?? '';

        return $object;
    }

    public function getContent(): string
    {
        $rowsForBody = [];
        foreach ($this->rows as $row) {
            $rowsForBody[] = $row->getContent();
        }

        $caption = '<caption>' . $this->caption . '</caption>';
        $body = (!empty($rowsForBody)) ? '<tbody>' . implode(PHP_EOL, $rowsForBody) . '</tbody>' : '';

        return '<table class="table table-striped table-bordered external-data-table">' . PHP_EOL . $caption . PHP_EOL . $body . PHP_EOL . '</table>' . PHP_EOL;
    }
}
