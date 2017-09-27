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
        // TODO: Implement createFromArray() method.
    }

    public function __toString()
    {
        $body = '<tbody>' . implode(PHP_EOL, $this->rows) . '</tbody>';
        $header = '<tbody>' . implode(PHP_EOL, $this->headerRows) . '</tbody>';
        $footer = '<tbody>' . implode(PHP_EOL, $this->footerRows) . '</tbody>';

        return '<table>' . PHP_EOL . $header . PHP_EOL . $footer . PHP_EOL . $body . PHP_EOL . '</table>' . PHP_EOL;
    }
}
