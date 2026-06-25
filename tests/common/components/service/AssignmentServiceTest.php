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
            new AssignmentEntityStub(Visibility::GameMaster, 1, 6, AssignmentRank::Major),
            new AssignmentEntityStub(Visibility::Full, 2, 6, AssignmentRank::Minor),
            new AssignmentEntityStub(Visibility::GameMaster, 3, 7, AssignmentRank::Major),
            new AssignmentEntityStub(Visibility::GameMaster, 4, 7, AssignmentRank::Minor),
            new AssignmentEntityStub(Visibility::GameMaster, 5, 7, AssignmentRank::Major),
        ];

        $assignmentQuery = $this->createMock(ActiveQuery::class);
        $assignmentQuery
            ->expects($this->exactly(2))
            ->method('all')
            ->willReturn($assignments);

        $actingIds = AssignmentService::extractAssignmentsActingIds($assignmentQuery);
        $narrativeIds = AssignmentService::extractAssignmentsNarrativeIds($assignmentQuery);

        self::assertSame([1, 3, 5], $actingIds->privateMajor);
        self::assertSame([], $actingIds->publicMajor);
        self::assertSame([4], $actingIds->privateMinor);
        self::assertSame([2], $actingIds->publicMinor);

        self::assertSame([6, 7], $narrativeIds->privateMajor);
        self::assertSame([], $narrativeIds->publicMajor);
        self::assertSame([7], $narrativeIds->privateMinor);
        self::assertSame([6], $narrativeIds->publicMinor);
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

        self::assertSame([], $actingIds->privateVital);
        self::assertSame([], $actingIds->privateMajor);
        self::assertSame([], $actingIds->privateMinor);
        self::assertSame([], $actingIds->privateOther);

        self::assertSame([], $actingIds->publicVital);
        self::assertSame([], $actingIds->publicMajor);
        self::assertSame([], $actingIds->publicMinor);
        self::assertSame([], $actingIds->publicOther);

        self::assertSame([], $narrativeIds->privateVital);
        self::assertSame([], $narrativeIds->privateMajor);
        self::assertSame([], $narrativeIds->privateMinor);
        self::assertSame([], $narrativeIds->privateOther);

        self::assertSame([], $narrativeIds->publicVital);
        self::assertSame([], $narrativeIds->publicMajor);
        self::assertSame([], $narrativeIds->publicMinor);
        self::assertSame([], $narrativeIds->publicOther);
    }
}
