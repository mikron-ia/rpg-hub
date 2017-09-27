<?php

namespace common\models\external;


use yii\base\Model;

class Table extends Model
{
    public $caption = '';
    public $headerTemplate = '[content]';
    public $bodyTemplate = '[content]';
    public $footerTemplate = '[content]';
    public $template = '[caption][header][body][footer]';
}
