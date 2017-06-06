<?php
namespace Msales\GrapesBundle\Tests\DependencyInjection;

use Msales\OptimizerBundle\Tests\Base\BaseTestCase;
use Mockery;
use Mockery\MockInterface;
use Msales\GrapesBundle\DependencyInjection\ServiceProviderDefinition;
use Msales\GrapesBundle\Exception\DefaultServiceAlreadySetException;
use Symfony\Component\DependencyInjection\Definition;

class ServiceProviderDefinitionTest extends BaseTestCase
{
    /** @var Definition|MockInterface */
    private $wrappedServiceProviderDefinition;

    /** @var ServiceProviderDefinition */
    private $grapesServiceProviderDefinition;

    /** @var Definition|MockInterface */
    private $passThroughValue;

    public function setUp()
    {
        $this->passThroughValue = Mockery::mock(Definition::class);
        $this->wrappedServiceProviderDefinition = $this->mockWrappedServiceProviderDefinition();
        $this->grapesServiceProviderDefinition = new ServiceProviderDefinition(
            $this->wrappedServiceProviderDefinition
        );
    }

    /**
     * Decorator should delegate 'addMethodCall' call to the wrapped instance.
     *
     * @dataProvider addMethodCallProvider
     *
     * @param string $methodName
     * @param array  $arguments
     */
    public function testDecoratorCanDelegateToTheWrappedInstance($methodName, array $arguments)
    {
        $result = $this->grapesServiceProviderDefinition->addMethodCall($methodName, $arguments);

        $this->assertMethodCalledWith(
            $this->wrappedServiceProviderDefinition,
            'addMethodCall',
            [$methodName, $arguments]
        );

        $this->assertSame(
            $result,
            $this->passThroughValue,
            '\'addMethodCall\' should delegate to \'addMethodCall\' of the wrapped instance.'
        );
    }

    /**
     * It should not be possible to add two default services to a service provider.
     */
    public function testDecoratorCanProtectAgainstAddingTwoDefaultServices()
    {
        $this->expectException(DefaultServiceAlreadySetException::class);

        $this->grapesServiceProviderDefinition->addMethodCall('register', ['alias1', 'tag1', true]);
        $this->grapesServiceProviderDefinition->addMethodCall('register', ['alias2', 'tag2', true]);
    }

    /**
     * @return array
     */
    public function addMethodCallProvider()
    {
        return [
            ['method1', ['alias1', 'tag1', false]],
            ['method2', ['alias2', 'tag2', true]],
            ['method3', ['alias3', 'tag3', false]],
            ['method4', ['alias4', 'tag4']],
            ['method5', ['alias5', 'tag5']],
        ];
    }

    private function mockWrappedServiceProviderDefinition()
    {
        $wrappedServiceProviderDefinition = Mockery::mock(Definition::class);

        $wrappedServiceProviderDefinition
            ->shouldReceive('addMethodCall')
            ->andReturn($this->passThroughValue)
        ;

        return $wrappedServiceProviderDefinition;
    }
}
