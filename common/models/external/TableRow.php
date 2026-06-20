<?php

namespace common\models\external;

use yii\base\Model;

class TableRow extends Model implements ExternalComponent
{
    /**
     * @var TableCell[]
     */
    public array $cells;

    public static function createFromData(array|string $data): ExternalComponent
    {
        $object = new TableRow();

        if (isset($data['cells'])) {
            $object->makeFromComplexArray($object, $data);
        } else {
            $object->makeFromSimpleArray($object, $data);
        }

        return $object;
    }

    /**
     * @param TableRow $object
     * @param array $data
     */
    private function makeFromComplexArray(TableRow $object, array $data): void
    {
        foreach ($data['cells'] ?? [] as $cell) {
            $object->cells[] = TableCell::createFromData($cell);
        }
    }

    /**
     * @param TableRow $object
     * @param array $data
     */
    private function makeFromSimpleArray(TableRow $object, array $data): void
    {
        foreach ($data ?? [] as $cell) {
            $object->cells[] = TableCell::createFromData($cell);
        }
    }

    public function getContent(): string
    {
        $cells = [];

        foreach ($this->cells ?? [] as $cell) {
            $cells[] = $cell->getContent();
        }

        return "<tr>" . implode($cells) . "</tr>";
    }
}
