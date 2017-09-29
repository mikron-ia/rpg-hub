<?php

namespace common\models\external;


use yii\base\Model;

class Table extends Model implements ExternalComponent
{
    public $caption = '';
    //public $headerTemplate = '[content]';
    //public $bodyTemplate = '[content]';
    //public $footerTemplate = '[content]';
    //public $template = '[caption][header][body][footer]';

    /**
     * @var TableRow[]
     */
    public $headerRows;

    /**
     * @var TableRow[]
     */
    public $footerRows;

    /**
     * @var TableRow[]
     */
    public $rows;

    /**
     * Creates object from array
     * @param array $array
     * @return ExternalComponent
     */
    static public function createFromArray(array $array): ExternalComponent
    {
        $object = new Table();

        $object->rows = [];
        foreach ($array['data']??[] as $record) {
            $object->rows[] = TableRow::createFromArray($record);
        }

        $object->headerRows = [];
        foreach ($array['header']??[] as $record) {
            $object->headerRows[] = TableRow::createFromArray($record);
        }

        $object->footerRows = [];
        foreach ($array['footer']??[] as $record) {
            $object->footerRows[] = TableRow::createFromArray($record);
        }

        $object->caption = $array['title']??'';

        return $object;
    }

    public function getContent()
    {
        $rowsForBody = [];
        foreach ($this->rows as $row) {
            $rowsForBody[] = $row->getContent();
        }

        $rowsForHeader = [];
        foreach ($this->headerRows as $row) {
            $rowsForHeader[] = $row->getContent();
        }

        $rowsForFooter = [];
        foreach ($this->footerRows as $row) {
            $rowsForFooter[] = $row->getContent();
        }

        $caption = '<caption>' . $this->caption . '</caption>';
        $body = (!empty($rowsForBody)) ? '<tbody>' . implode(PHP_EOL, $rowsForBody) . '</tbody>' : '';
        $header = (!empty($rowsForHeader)) ? '<thead>' . implode(PHP_EOL, $rowsForHeader) . '</thead>' : '';
        $footer = (!empty($rowsForFooter)) ? '<tfoot>' . implode(PHP_EOL, $rowsForFooter) . '</tfoot>' : '';

        return '<table class="table table-striped table-bordered">' . PHP_EOL . $caption . PHP_EOL . $header . PHP_EOL . $footer . PHP_EOL . $body . PHP_EOL . '</table>' . PHP_EOL;
    }
}
