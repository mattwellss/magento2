<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Magento\Framework\Console;

use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Class CommandLoader allows for deferred initialization of Symfony Commands
 */
class CommandLoader implements CommandLoaderInterface
{

    /**
     * List of commands in the format [ 'command:name' => 'Fully\Qualified\ClassName' ]
     * @var array
     */
    private $commands;

    /** @var ObjectManagerInterface */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager, array $commands)
    {
        $this->objectManager = $objectManager;
        $this->commands = array_combine(array_column($commands, 'name'), array_column($commands, 'class'));
    }

    /**
     * @param string $name
     * @return \Symfony\Component\Console\Command\Command
     * @throws CommandNotFoundException
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->objectManager->create($this->commands[$name]);
        }
        throw new CommandNotFoundException(sprintf('Command "%s" does not exist.', $name));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->commands[$name]);
    }

    /**
     * @return string[]
     */
    public function getNames()
    {
        return array_keys($this->commands);
    }
}
