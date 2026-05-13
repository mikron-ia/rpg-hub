<?php

namespace common\components\service;

use common\models\core\Visibility;
use common\models\exceptions\InternalErrorException;
use common\_stubs\AssignmentEntityStub;
use PHPUnit\Framework\TestCase;
use yii\db\ActiveQuery;

final class AssignmentServiceTest extends TestCase
{
    /**
     * @throws InternalErrorException
     */
    public function testDistributeAssignmentsGroupsEntityIdsByVisibility(): void
    {
        $assignments = [
            new AssignmentEntityStub(Visibility::VISIBILITY_GM, 1, 6),
            new AssignmentEntityStub(Visibility::VISIBILITY_FULL, 2, 6),
            new AssignmentEntityStub(Visibility::VISIBILITY_GM, 3, 7),
            new AssignmentEntityStub(Visibility::VISIBILITY_GM, 4, 7),
            new AssignmentEntityStub(Visibility::VISIBILITY_GM, 5, 7),
        ];

        $assignmentQuery = $this->createMock(ActiveQuery::class);
        $assignmentQuery
            ->expects($this->exactly(2))
            ->method('all')
            ->willReturn($assignments);

        self::assertSame(
            [
                Visibility::VISIBILITY_GM->value => [1, 3, 4, 5],
                Visibility::VISIBILITY_FULL->value => [2],
            ],
            AssignmentService::distributeAssignmentsActingIds($assignmentQuery)
        );

        self::assertSame(
            [
                Visibility::VISIBILITY_GM->value => [6, 7],
                Visibility::VISIBILITY_FULL->value => [6],
            ],
            AssignmentService::distributeAssignmentsNarrativeIds($assignmentQuery)
        );
    }

    /**
     * @throws InternalErrorException
     */
    public function testEmpty(): void
    {
        $assignmentQuery = $this->createMock(ActiveQuery::class);
        $assignmentQuery
            ->expects($this->exactly(2))
            ->method('all')
            ->willReturn([]);

        self::assertSame(
            [
                Visibility::VISIBILITY_GM->value => [],
                Visibility::VISIBILITY_FULL->value => [],
            ],
            AssignmentService::distributeAssignmentsActingIds($assignmentQuery)
        );

        self::assertSame(
            [
                Visibility::VISIBILITY_GM->value => [],
                Visibility::VISIBILITY_FULL->value => [],
            ],
            AssignmentService::distributeAssignmentsNarrativeIds($assignmentQuery)
        );
    }
}
