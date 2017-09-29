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

    static public function createFromArray(array $array): ExternalComponent
    {
        $object = new TableRow([
            'cells' => $array
        ]);

        return $object;
    }

    public function getContent()
    {
        return "<tr><td>" . implode("</td><td>", $this->cells) . "</td></tr>";
    }
}
