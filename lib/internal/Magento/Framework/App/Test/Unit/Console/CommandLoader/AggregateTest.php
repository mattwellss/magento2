<?php

namespace Magento\Framework\App\Test\Unit\Console\CommandLoader;

use Magento\Framework\Console\CommandLoader\Aggregate;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class AggregateTest extends TestCase
{
    private $firstCommandLoader;
    private $secondCommandLoader;
    private $subj;

    protected function setUp(): void
    {
        $this->firstCommandLoader = $this->getMockBuilder(CommandLoaderInterface::class)->getMock();
        $this->secondCommandLoader = $this->getMockBuilder(CommandLoaderInterface::class)->getMock();
        $this->subj = new Aggregate([$this->firstCommandLoader, $this->secondCommandLoader]);
    }

    /**
     * @dataProvider provideTestCasesForHas
     */
    public function testHas($firstResult, $secondResult, $overallResult)
    {
        $this->firstCommandLoader->method('has')->with('foo')->willReturn($firstResult);
        $this->secondCommandLoader->method('has')->with('foo')->willReturn($secondResult);

        static::assertEquals($overallResult, $this->subj->has('foo'));
    }


    /**
     * @dataProvider provideTestCasesForGet
     */
    public function testGet($firstCmd, $secondCmd)
    {
        $firstHas = (bool)$firstCmd;
        $secondHas = (bool)$secondCmd;
        $this->firstCommandLoader->method('has')->with('foo')->willReturn($firstHas);
        $this->firstCommandLoader->method('get')->with('foo')->willReturn($firstCmd);
        $this->secondCommandLoader->method('has')->with('foo')->willReturn($secondHas);
        $this->secondCommandLoader->method('get')->with('foo')->willReturn($secondCmd);

        static::assertInstanceOf(Command::class, $this->subj->get('foo'));
    }

    public function provideTestCasesForGet()
    {
        return [
            [
                new Command(),
                null
            ],
            [
                null,
                new Command()
            ]
        ];
    }

    public function testGetThrow()
    {
        $this->firstCommandLoader->method('has')->with('foo')->willReturn(false);
        $this->secondCommandLoader->method('has')->with('foo')->willReturn(false);

        $this->expectException(CommandNotFoundException::class);
        $this->subj->get('foo');
    }

    public function testGetNames()
    {
        $this->firstCommandLoader->method('getNames')->willReturn(['foo', 'bar']);
        $this->secondCommandLoader->method('getNames')->willReturn(['baz', 'qux']);

        static::assertEquals(['foo', 'bar', 'baz', 'qux'], $this->subj->getNames());
    }

    public function provideTestCasesForHas()
    {
        return [
            [true, false, true],
            [false, true, true],
            [false, false, false]
        ];
    }
}
