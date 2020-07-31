<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Magento\Framework\Console\CommandLoader;

use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Class Aggregate has a list of command loaders, which can be extended via DI configuration.
 */
class Aggregate implements CommandLoaderInterface
{
    /** @var CommandLoaderInterface[] */
    private $commandLoaders;

    public function __construct($commandLoaders = [])
    {
        $this->commandLoaders = $commandLoaders;
    }

    /**
     * @param string $name
     * @return \Symfony\Component\Console\Command\Command
     */
    public function get($name)
    {
        foreach ($this->commandLoaders as $commandLoader) {
            if ($commandLoader->has($name)) {
                return $commandLoader->get($name);
            }
        }

        throw new CommandNotFoundException(sprintf('Command "%s" does not exist.', $name));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        foreach ($this->commandLoaders as $commandLoader) {
            if ($commandLoader->has($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function getNames()
    {
        return array_merge([], ...array_map(function (CommandLoaderInterface $commandLoader) {
            return $commandLoader->getNames();
        }, $this->commandLoaders));
    }
}
