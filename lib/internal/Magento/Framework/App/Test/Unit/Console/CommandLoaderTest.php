<?php

namespace Magento\Framework\App\Test\Unit\Console;

use Magento\Framework\Console\CommandLoader;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class CommandLoaderTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|ObjectManagerInterface */
    private $objectManagerMock;

    protected function setUp(): void
    {
        $this->objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)->getMock();
    }

    public function testHasWithZeroCommands()
    {
        $subj = new CommandLoader($this->objectManagerMock, []);

        static::assertFalse($subj->has('foo'));
    }

    public function testHasWithAtLeastOneCommand()
    {
        $subj = new CommandLoader($this->objectManagerMock, [
            [
                'name' => 'foo',
                'class' => FooCommand::class
            ]
        ]);

        static::assertTrue($subj->has('foo'));
    }

    public function testGetWithZeroCommands()
    {
        $subj = new CommandLoader($this->objectManagerMock, []);

        $this->expectException(CommandNotFoundException::class);

        $subj->get('foo');
    }

    public function testGetWithAtLeastOneCommand()
    {
        $this->objectManagerMock
            ->method('create')
            ->with(FooCommand::class)
            ->willReturn(new FooCommand());

        $subj = new CommandLoader($this->objectManagerMock, [
            [
                'name' => 'foo',
                'class' => FooCommand::class
            ]
        ]);

        static::assertInstanceOf(FooCommand::class, $subj->get('foo'));
    }

    public function testGetNamesWithZeroCommands()
    {
        $subj = new CommandLoader($this->objectManagerMock, []);

        static::assertEquals([], $subj->getNames());
    }

    public function testGetNames()
    {
        $subj = new CommandLoader($this->objectManagerMock, [
            [
                'name' => 'foo',
                'class' => FooCommand::class
            ],
            [
                'name' => 'bar',
                'class' => BarCommand::class
            ]
        ]);

        static::assertEquals(['foo', 'bar'], $subj->getNames());
    }
}

class FooCommand extends Command
{
}
