<?php

namespace common\dto;

final readonly class AssignmentIdentifierLists
{
    public function __construct(public array $public, public array $private)
    {
    }
}
