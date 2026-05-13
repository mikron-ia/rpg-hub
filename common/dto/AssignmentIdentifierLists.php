<?php

namespace common\dto;

final readonly class AssignmentIdentifierLists
{
    public function __construct(
        public array $publicVital,
        public array $publicMajor,
        public array $publicMinor,
        public array $publicOther,
        public array $privateVital,
        public array $privateMajor,
        public array $privateMinor,
        public array $privateOther,
    ) {
    }
}
