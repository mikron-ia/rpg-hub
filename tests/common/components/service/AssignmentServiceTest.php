<?php

namespace common\components\service;

use common\models\core\Visibility;
use common\_stubs\AssignmentEntityStub;
use common\models\type\AssignmentRank;
use PHPUnit\Framework\TestCase;
use yii\db\ActiveQuery;

final class AssignmentServiceTest extends TestCase
{
    public function testSuccess(): void
    {
        $assignments = [
            new AssignmentEntityStub(Visibility::VISIBILITY_GM, 1, 6, AssignmentRank::Major),
            new AssignmentEntityStub(Visibility::VISIBILITY_FULL, 2, 6, AssignmentRank::Minor),
            new AssignmentEntityStub(Visibility::VISIBILITY_GM, 3, 7, AssignmentRank::Major),
            new AssignmentEntityStub(Visibility::VISIBILITY_GM, 4, 7, AssignmentRank::Minor),
            new AssignmentEntityStub(Visibility::VISIBILITY_GM, 5, 7, AssignmentRank::Major),
        ];

        $assignmentQuery = $this->createMock(ActiveQuery::class);
        $assignmentQuery
            ->expects($this->exactly(2))
            ->method('all')
            ->willReturn($assignments);

        $actingIds = AssignmentService::extractAssignmentsActingIds($assignmentQuery);
        $narrativeIds = AssignmentService::extractAssignmentsNarrativeIds($assignmentQuery);

        self::assertSame([1, 3, 4, 5], $actingIds->private);
        self::assertSame([2], $actingIds->public);

        self::assertSame([6, 7], $narrativeIds->private);
        self::assertSame([6], $narrativeIds->public);
    }

    public function testEmpty(): void
    {
        $assignmentQuery = $this->createMock(ActiveQuery::class);
        $assignmentQuery
            ->expects($this->exactly(2))
            ->method('all')
            ->willReturn([]);

        $actingIds = AssignmentService::extractAssignmentsActingIds($assignmentQuery);

        $narrativeIds = AssignmentService::extractAssignmentsNarrativeIds($assignmentQuery);

        self::assertSame([], $actingIds->private);
        self::assertSame([], $actingIds->public);

        self::assertSame([], $narrativeIds->private);
        self::assertSame([], $narrativeIds->public);
    }
}
