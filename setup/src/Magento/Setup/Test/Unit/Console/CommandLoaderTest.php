<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Console;

use Laminas\ServiceManager\ServiceManager;
use Magento\Setup\Console\CommandLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommandLoaderTest extends TestCase
{
    /**
     * @var MockObject|CommandLoader
     */
    private $commandLoader;

    /**
     * @var MockObject|ServiceManager
     */
    private $serviceManager;

    protected function setUp(): void
    {
        $this->serviceManager = $this->createMock(ServiceManager::class);
        $this->commandLoader = new CommandLoader($this->serviceManager);
    }

    public function testServiceManagerIsUsedToInitializeCommands()
    {
        $this->serviceManager->expects($this->once())
            ->method('get');

        $firstCommandName = current($this->commandLoader->getNames());
        $this->commandLoader->get($firstCommandName);
    }
}
