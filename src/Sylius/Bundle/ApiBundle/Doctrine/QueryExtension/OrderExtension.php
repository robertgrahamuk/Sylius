<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\OrderInterface;

/** @experimental */
final class OrderExtension implements ContextAwareQueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * @param array<string> $orderStatesToFilterOut
     */
    public function __construct(
        private UserContextInterface $userContext,
        private array $orderStatesToFilterOut,
    ) {
    }

    /**
     * @param array<mixed> $context
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, OrderInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();
        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {

            $stateParameter = $queryNameGenerator->generateParameterName('state');
            $rootAlias = $queryBuilder->getRootAliases()[0];

            $queryBuilder
                ->andWhere(sprintf('%s.state != :%s', $rootAlias, $stateParameter))
                ->setParameter($stateParameter, $this->orderStatesToFilterOut)
            ;
        }
    }

    /**
     * @param array<mixed> $context
     * @param array<mixed> $identifiers
     */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        $this->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
    }
}
