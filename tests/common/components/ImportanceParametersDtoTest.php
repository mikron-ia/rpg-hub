<?php

namespace common\components;

use common\models\core\ImportanceCategory;
use common\models\exceptions\InvalidBackendConfigurationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ImportanceParametersDtoTest extends TestCase
{
    public function testCreationValidConfig(): void
    {
        $importanceCategoryWeightMap = [
            ImportanceCategory::IMPORTANCE_EXTREME->value => 8,
            ImportanceCategory::IMPORTANCE_HIGH->value => 4,
            ImportanceCategory::IMPORTANCE_MEDIUM->value => 2,
            ImportanceCategory::IMPORTANCE_LOW->value => 1,
            ImportanceCategory::IMPORTANCE_NONE->value => 0,
        ];

        $data = [
            'importanceWeights' => [
                'importanceCategory' => $importanceCategoryWeightMap,
                'newAndUpdated' => [
                    'new' => 16,
                    'updated' => 4,
                    'default' => 0,
                ],
                'associated' => [
                    'associated' => 4,
                    'unassociated' => -4,
                ],
                'date' => [
                    'initial' => 8,
                    'divider' => 2,
                ],
            ],
        ];

        $dto = ImportanceParametersDto::create($data);

        $this->assertSame($importanceCategoryWeightMap, $dto->importanceCategoryMap);
        $this->assertSame(16, $dto->newValue);
        $this->assertSame(4, $dto->updatedValue);
        $this->assertSame(0, $dto->defaultValue);
        $this->assertSame(4, $dto->associatedValue);
        $this->assertSame(-4, $dto->unassociatedValue);
        $this->assertSame(8, $dto->dateInitial);
        $this->assertSame(2, $dto->dateDivider);
    }

    #[DataProvider('invalidConfigDataProvider')]
    public function testCreationInvalidConfig(array $data, string $missingField): void
    {
        $this->expectException(InvalidBackendConfigurationException::class);
        $this->expectExceptionMessage(sprintf('Invalid importance configuration: Undefined array key "%s"', $missingField));
        ImportanceParametersDto::create($data);
    }

    public static function invalidConfigDataProvider(): array
    {
        return [
            [[], 'importanceWeights'],
            [
                [
                    'importanceWeights' => [
                        'newAndUpdated' => [
                            'new' => 16,
                            'updated' => 4,
                            'default' => 0,
                        ],
                        'associated' => [
                            'associated' => 4,
                            'unassociated' => -4,
                        ],
                        'date' => [
                            'initial' => 8,
                            'divider' => 2,
                        ],
                    ],
                ],
                'importanceCategory',
            ],
            [
                [
                    'importanceWeights' => [
                        'importanceCategory' => [],
                        'newAndUpdated' => [
                            'updated' => 4,
                            'default' => 0,
                        ],
                        'associated' => [
                            'associated' => 4,
                            'unassociated' => -4,
                        ],
                        'date' => [
                            'initial' => 8,
                            'divider' => 2,
                        ],
                    ],
                ],
                'new',
            ],
            [
                [
                    'importanceWeights' => [
                        'importanceCategory' => [],
                        'newAndUpdated' => [
                            'new' => 16,
                            'default' => 0,
                        ],
                        'associated' => [
                            'associated' => 4,
                            'unassociated' => -4,
                        ],
                        'date' => [
                            'initial' => 8,
                            'divider' => 2,
                        ],
                    ],
                ],
                'updated',
            ],
            [
                [
                    'importanceWeights' => [
                        'importanceCategory' => [],
                        'newAndUpdated' => [
                            'new' => 16,
                            'updated' => 4,
                        ],
                        'associated' => [
                            'associated' => 4,
                            'unassociated' => -4,
                        ],
                        'date' => [
                            'initial' => 8,
                            'divider' => 2,
                        ],
                    ],
                ],
                'default',
            ],
            [
                [
                    'importanceWeights' => [
                        'importanceCategory' => [],
                        'newAndUpdated' => [
                            'new' => 16,
                            'updated' => 4,
                            'default' => 0,
                        ],
                        'associated' => [
                            'unassociated' => -4,
                        ],
                        'date' => [
                            'initial' => 8,
                            'divider' => 2,
                        ],
                    ],
                ],
                'associated',
            ],
            [
                [
                    'importanceWeights' => [
                        'importanceCategory' => [],
                        'newAndUpdated' => [
                            'new' => 16,
                            'updated' => 4,
                            'default' => 0,
                        ],
                        'associated' => [
                            'associated' => 4,
                        ],
                        'date' => [
                            'initial' => 8,
                            'divider' => 2,
                        ],
                    ],
                ],
                'unassociated',
            ],
            [
                [
                    'importanceWeights' => [
                        'importanceCategory' => [],
                        'newAndUpdated' => [
                            'new' => 16,
                            'updated' => 4,
                            'default' => 0,
                        ],
                        'associated' => [
                            'associated' => 4,
                            'unassociated' => -4,
                        ],
                        'date' => [
                            'divider' => 2,
                        ],
                    ],
                ],
                'initial',
            ],
            [
                [
                    'importanceWeights' => [
                        'importanceCategory' => [],
                        'newAndUpdated' => [
                            'new' => 16,
                            'updated' => 4,
                            'default' => 0,
                        ],
                        'associated' => [
                            'associated' => 4,
                            'unassociated' => -4,
                        ],
                        'date' => [
                            'initial' => 8,
                        ],
                    ],
                ],
                'divider',
            ],
        ];
    }
}
