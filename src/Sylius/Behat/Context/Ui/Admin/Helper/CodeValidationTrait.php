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

namespace Sylius\Behat\Context\Ui\Admin\Helper;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Webmozart\Assert\Assert;

trait CodeValidationTrait
{
    /**
     * @When I specify a too long code
     */
    public function iSpecifyATooLongCode(): void
    {
        $this->iSpecifyItsCodeAs(str_repeat('a', $this->getMaxCodeLength() + 1));
    }

    /**
     * @Then I should be notified that the code is too long
     */
    public function iShouldBeNotifiedThatTheCodeIsTooLong(): void
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::contains(
            $currentPage->getValidationMessage('code'),
            'The code must not be longer than',
        );
    }

    abstract protected function iSpecifyItsCodeAs(?string $code = null): void;

    abstract protected function resolveCurrentPage(): SymfonyPageInterface;

    private function getMaxCodeLength(): int
    {
        return 255;
    }
}
