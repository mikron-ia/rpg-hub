<?php

declare(strict_types=1);

namespace common\dto;

use yii\data\ActiveDataProvider;

final readonly class CharacterListDataObject
{
    public function __construct(public string $name, public ActiveDataProvider $dataProvider)
    {
    }
}
