<?php

/*
 * This file is part of the RequestLab package.
 *
 * (c) RequestLab <hello@requestlab.fr>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace RequestLab\EstatBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Widop\HttpAdapterBundle\DependencyInjection\WidopHttpAdapterExtension;
use RequestLab\EstatBundle\DependencyInjection\RequestLabEstatExtension;

/**
 * Abstract RequestLab Estat extension test.
 *
 * @author Yann Lecommandoux <yann@requestlab.fr>
 */
abstract class AbstractRequestLabEstatExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder */
    protected $container;

    /**
     * {@ineritdoc}
     */
    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('bundle.dir', realpath(__DIR__.'/../../'));
        $this->container->registerExtension($extension = new WidopHttpAdapterExtension());
        $this->container->loadFromExtension($extension->getAlias());
        $this->container->registerExtension(new RequestLabEstatExtension());
    }

    /**
     * {@ineritdoc}
     */
    protected function tearDown()
    {
        unset($this->container);
    }

    /**
     * Loads a configuration.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container     The container builder.
     * @param string                                                  $configuration The configuration name.
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $configuration);

    public function testEstatService()
    {
        $this->loadConfiguration($this->container, 'estat');
        $this->container->compile();

        $estat = $this->container->get('request_lab_estat');

        $this->assertInstanceOf('RequestLab\Estat\Service', $estat);
        $this->assertSame('Login', $estat->getClient()->getLogin());
        $this->assertSame('Password', $estat->getClient()->getPassword());
    }

    public function testQueryService()
    {
        $this->loadConfiguration($this->container, 'estat');
        $this->container->compile();

        $query = $this->container->get('request_lab_estat.query');

        $this->assertInstanceOf('RequestLab\Estat\Query', $query);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoginRequired()
    {
        $this->loadConfiguration($this->container, 'login');
        $this->container->compile();
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testPasswordRequired()
    {
        $this->loadConfiguration($this->container, 'password');
        $this->container->compile();
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testInvalidHttpAdapter()
    {
        $this->loadConfiguration($this->container, 'http_adapter');
        $this->container->compile();
    }
}