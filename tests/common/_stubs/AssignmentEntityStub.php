<?php

namespace common\_stubs;

use common\models\core\HasVisibility;
use common\models\core\IsAssignment;
use common\models\core\Visibility;
use Override;

final readonly class AssignmentEntityStub implements HasVisibility, IsAssignment
{
    public function __construct(
        private Visibility $visibility,
        private int $actingSideId,
        private int $narrativeSideId,
    ) {
    }

    #[Override]
    public static function create(int $actingSideId, int $narrativeSideId, Visibility $visibility): self
    {
        return new self($visibility, $actingSideId, $narrativeSideId);
    }

    #[Override]
    public static function allowedVisibilities(): array
    {
        return [];
    }

    #[Override]
    public function getVisibility(): Visibility
    {
        return $this->visibility;
    }

    #[Override]
    public function getVisibilityName(): string
    {
        return $this->visibility->value;
    }

    #[Override]
    public function getVisibilityLowercase(): string
    {
        return $this->visibility->value;
    }

    #[Override]
    public function getActingSideId(): int
    {
        return $this->actingSideId;
    }

    #[Override]
    public function getNarrativeSideId(): int
    {
        return $this->narrativeSideId;
    }
}
