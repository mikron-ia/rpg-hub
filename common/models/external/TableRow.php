<?php
/**
 * Created by PhpStorm.
 * User: Wilk
 * Date: 2017-09-27
 * Time: 19:56
 */

namespace common\models\external;


use yii\base\Model;

class TableRow extends Model implements ExternalComponent
{
    /**
     * @var string
     */
    public $cells;

    /**
     * @var string
     */
    public $cellTag;

    static public function createFromArray(array $array): ExternalComponent
    {
        $object = new TableRow();

        $object->cellTag = $array['tag'] ?? 'td';
        unset($array['tag']);

        $object->cells = $array;

        return $object;
    }

    public function getContent()
    {
        return "<tr><" . $this->cellTag . ">" .
            implode("</" . $this->cellTag . "><" . $this->cellTag . ">", $this->cells) .
            "</" . $this->cellTag . "></tr>";
    }
}
