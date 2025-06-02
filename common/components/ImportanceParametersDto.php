<?php

namespace common\components;

use common\models\exceptions\InvalidBackendConfigurationException;
use ErrorException;

final readonly class ImportanceParametersDto
{
    private function __construct(
        public array $importanceCategoryMap,
        public int $newValue,
        public int $updatedValue,
        public int $defaultValue,
        public int $associatedValue,
        public int $unassociatedValue,
        public int $dateInitial,
        public int $dateDivider,
    ) {
    }

    /**
     * @param array<string,mixed> $importanceParameters
     *
     * @return self
     *
     * @throws InvalidBackendConfigurationException
     */
    public static function create(array $importanceParameters): self
    {
        try {
            return new self(
                $importanceParameters['importanceWeights']['importanceCategory'],
                $importanceParameters['importanceWeights']['newAndUpdated']['new'],
                $importanceParameters['importanceWeights']['newAndUpdated']['updated'],
                $importanceParameters['importanceWeights']['newAndUpdated']['default'],
                $importanceParameters['importanceWeights']['associated']['associated'],
                $importanceParameters['importanceWeights']['associated']['unassociated'],
                $importanceParameters['importanceWeights']['date']['initial'],
                $importanceParameters['importanceWeights']['date']['divider'],
            );
        } catch (ErrorException $e) {
            throw new InvalidBackendConfigurationException(
                sprintf('Invalid importance configuration: %s', $e->getMessage())
            );
        }
    }
}
