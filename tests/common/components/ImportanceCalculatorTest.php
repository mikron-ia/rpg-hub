<?php

namespace common\components;

use common\models\core\HasImportance;
use common\models\core\ImportanceCategory;
use common\models\User;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ImportanceCalculatorTest extends TestCase
{
    private ImportanceCalculator $calculator;

    protected function setUp(): void
    {
        $data = [
            'importanceWeights' => [
                'importanceCategory' => [
                    ImportanceCategory::IMPORTANCE_EXTREME->value => 8,
                    ImportanceCategory::IMPORTANCE_HIGH->value => 4,
                    ImportanceCategory::IMPORTANCE_MEDIUM->value => 2,
                    ImportanceCategory::IMPORTANCE_LOW->value => 1,
                    ImportanceCategory::IMPORTANCE_NONE->value => 0,
                ],
                'newAndUpdated' => [
                    'new' => 15,
                    'updated' => 5,
                    'default' => 0,
                ],
                'associated' => [
                    'associated' => 3,
                    'unassociated' => 0,
                ],
                'date' => [
                    'initial' => 8,
                    'divider' => 2,
                ],
            ],
        ];

        $this->calculator = new ImportanceCalculator(ImportanceParametersDto::create($data));

        parent::setUp();
    }

    #[DataProvider('calculationDataProvider')]
    public function testCalculate(
        ImportanceCategory $importanceCategoryObject,
        string $lastModified,
        string $seenStatusForUser,
        int $expectedImportance,
    ): void {
        $measuredObject = $this->createMock(HasImportance::class);

        $measuredObject->method('getImportanceCategoryObject')->willReturn($importanceCategoryObject);
        $measuredObject->method('getLastModified')->willReturn(new DateTimeImmutable($lastModified));
        $measuredObject->method('getSeenStatusForUser')->willReturn($seenStatusForUser);

        $user = new User();
        $user->id = 0;

        $this->assertSame(
            $expectedImportance,
            $this->calculator->calculate($measuredObject, $user, new DateTimeImmutable('2020-01-01 00:00:00')),
        );
    }

    public static function calculationDataProvider(): array
    {
        return [
            'baseline' => [ImportanceCategory::IMPORTANCE_NONE, '2020-01-01 00:00:00', 'seen', 8], // none, now, seen
            'category-low' => [ImportanceCategory::IMPORTANCE_LOW, '2020-01-01 00:00:00', 'seen', 9],
            'category-medium' => [ImportanceCategory::IMPORTANCE_MEDIUM, '2020-01-01 00:00:00', 'seen', 10],
            'category-high' => [ImportanceCategory::IMPORTANCE_HIGH, '2020-01-01 00:00:00', 'seen', 12],
            'category-extreme' => [ImportanceCategory::IMPORTANCE_EXTREME, '2020-01-01 00:00:00', 'seen', 16],
            'sighting-updated' => [ImportanceCategory::IMPORTANCE_NONE, '2020-01-01 00:00:00', 'updated', 13],
            'sighting-new' => [ImportanceCategory::IMPORTANCE_NONE, '2020-01-01 00:00:00', 'new', 23],
            'age-day' => [ImportanceCategory::IMPORTANCE_NONE, '2020-01-02 00:00:00', 'seen', 4],
            'age-month' => [ImportanceCategory::IMPORTANCE_NONE, '2020-02-01 00:00:00', 'seen', 2],
            'age-year' => [ImportanceCategory::IMPORTANCE_NONE, '2021-01-01 00:00:00', 'seen', 1],
        ];
    }
}
