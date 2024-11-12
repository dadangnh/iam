<?php
namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;

class NullFilter extends AbstractFilter
{
    /**
     * Filters the query based on a null condition.
     *
     * @param QueryBuilder $queryBuilder
     * @param string $alias
     * @param string $field
     * @param mixed $value
     */
    protected function prepareQueryBuilder(QueryBuilder $queryBuilder, $alias, $field, $value)
    {
        // Apply the null condition only if the value is 'null'
        if ($value === 'null') {
            // Directly use the alias and field to apply the IS NULL condition
            $queryBuilder->andWhere(sprintf('%s.%s IS NULL', $alias, $field));
        } else {
            throw new \InvalidArgumentException('Invalid filter value');
        }
    }

    /**
     * Filter property for a null condition.
     *
     * @param string $property
     * @param mixed $value
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param Operation|null $operation
     * @param array $context
     */
    protected function filterProperty(
        string $property,
               $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        // Apply the filter only to the 'tanggalNonaktif' property
        if ($property === 'tanggalNonaktif') {
            // Get the root alias of the query (i.e., the entity we are querying)
            $alias = $queryBuilder->getRootAlias();

            // Call the method to prepare the query for the 'tanggalNonaktif' field
            $this->prepareQueryBuilder($queryBuilder, $alias, $property, $value);
        }
    }

    /**
     * Describe the filter in the API documentation.
     *
     * @param string $resourceClass
     * @return array
     */
    public function getDescription(string $resourceClass): array
    {
        return [
            'tanggalNonaktif' => [
                'property' => 'tanggalNonaktif',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Filter by null tanggalNonaktif',
                    'type' => 'string',
                ],
            ],
        ];
    }
}
