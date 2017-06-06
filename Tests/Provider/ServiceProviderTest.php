<?php
namespace Msales\GrapesBundle\Tests\ServiceProvider;

use Msales\OptimizerBundle\Tests\Base\BaseTestCase;
use Msales\GrapesBundle\Exception\AliasNotRegisteredException;
use Msales\GrapesBundle\Provider\ServiceProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use stdClass;

class ServiceProviderTest extends BaseTestCase
{
    /** @var ContainerInterface|MockInterface */
    private $serviceContainer;

    /** @var ServiceProvider */
    private $serviceProvider;

    public function setUp()
    {
        $this->serviceProvider = new ServiceProvider();
    }

    /**
     * @test
     *
     * @dataProvider registerServicesProvider
     *
     * @param array         $servicesToBeRegistered Arguments to register services without a default service
     * @param string        $getAlias
     * @param string|null   $expectServiceID
     * @param stdClass|null $expectedServiceMock
     * @param string        $message
     */
    public function getServiceFromProviderByAlias(
        array $servicesToBeRegistered,
        $getAlias,
        $expectServiceID,
        $expectedServiceMock,
        $message
    ) {
        $this->registerServices($servicesToBeRegistered);

        $requestingKnownService = !is_null($expectServiceID);
        if (!$requestingKnownService) {
            /*
             * We need to check both types of exception.
             */
            $this->expectException(ServiceNotFoundException::class);
            $this->expectException(AliasNotRegisteredException::class);
        }

        $service = $this->serviceProvider->get($getAlias);

        if ($requestingKnownService) {
            $this->assertSame($expectedServiceMock, $service, $message);
        }
    }

    /**
     * @return array
     */
    public function registerServicesProvider()
    {
        $service1 = json_decode('{"name": "pass-through Service1 mock"}');
        $service2 = json_decode('{"name": "pass-through Service2 mock"}');
        $service3 = json_decode('{"name": "pass-through Service3 mock"}');

        $servicesToBeRegisteredWithoutDefault = [
            'alias1' => [
                'alias'       => 'alias1',
                'serviceID'   => 'taggedServiceID1',
                'default'     => false,
                'serviceMock' => $service1,
            ],
            'alias2' => [
                'alias'       => 'alias2',
                'serviceID'   => 'taggedServiceID2',
                'default'     => false,
                'serviceMock' => $service2,
            ],
            'alias3' => [
                'alias'       => 'alias3',
                'serviceID'   => 'taggedServiceID3',
                'default'     => false,
                'serviceMock' => $service3,
            ],
        ];

        $servicesToBeRegisteredWithDefault = [
            'alias1' => [
                'alias'       => 'alias1',
                'serviceID'   => 'taggedServiceID1',
                'default'     => false,
                'serviceMock' => $service1,
            ],
            'alias2' => [
                'alias'       => 'alias2',
                'serviceID'   => 'taggedServiceID2',
                'default'     => true,
                'serviceMock' => $service2,
            ],
            'alias3' => [
                'alias'       => 'alias3',
                'serviceID'   => 'taggedServiceID3',
                'default'     => false,
                'serviceMock' => $service3,
            ],
        ];

        $onlyDefaultService = [
            'alias1' => [
                'alias'       => 'alias1',
                'serviceID'   => 'taggedServiceID1',
                'default'     => true,
                'serviceMock' => $service1,
            ],
        ];

        return [
            /*
             * Testing without default
             */
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithoutDefault,
                'getAlias'               => 'alias1',
                'expectServiceID'        => 'taggedServiceID1',
                'expectedService'        => $servicesToBeRegisteredWithoutDefault['alias1']['serviceMock'],
                'message'                => 'Service registered with \'alias1\' should be returned for \'alias1\'',
            ],
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithoutDefault,
                'getAlias'               => 'alias2',
                'expectServiceID'        => 'taggedServiceID2',
                'expectedService'        => $servicesToBeRegisteredWithoutDefault['alias2']['serviceMock'],
                'message'                => 'Service registered with \'alias2\' should be returned for \'alias2\'',
            ],
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithoutDefault,
                'getAlias'               => 'alias3',
                'expectServiceID'        => 'taggedServiceID3',
                'expectedService'        => $servicesToBeRegisteredWithoutDefault['alias3']['serviceMock'],
                'message'                => 'Service registered with \'alias3\' should be returned for \'alias3\'',
            ],
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithoutDefault,
                'getAlias'               => 'not-existing-alias',
                'expectServiceID'        => null,
                'expectedService'        => null,
                'message'                => '',
            ],
            /*
             * Testing with default
             */
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithDefault,
                'getAlias'               => 'alias1',
                'expectServiceID'        => 'taggedServiceID1',
                'expectedService'        => $servicesToBeRegisteredWithDefault['alias1']['serviceMock'],
                'message'                => 'Service registered with \'alias1\' should be returned for \'alias1\'',
            ],
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithDefault,
                'getAlias'               => 'alias2',
                'expectServiceID'        => 'taggedServiceID2',
                'expectedService'        => $servicesToBeRegisteredWithDefault['alias2']['serviceMock'],
                'message'                => 'Service registered with \'alias2\' should be returned for \'alias2\'',
            ],
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithDefault,
                'getAlias'               => 'alias3',
                'expectServiceID'        => 'taggedServiceID3',
                'expectedService'        => $servicesToBeRegisteredWithDefault['alias3']['serviceMock'],
                'message'                => 'Service registered with \'alias3\' should be returned for \'alias3\'',
            ],
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithDefault,
                'getAlias'               => 'not-existing-alias',
                'expectServiceID'        => 'taggedServiceID2',
                'expectedService'        => $servicesToBeRegisteredWithDefault['alias2']['serviceMock'],
                'message'                => 'Default service should be returned if using non-existing-alias',
            ],
            /*
             * With no service registered
             */
            [
                'servicesToBeRegistered' => [],
                'getAlias'               => 'not-existing-alias',
                'expectServiceID'        => null,
                'expectedService'        => null,
                'message'                => '',
            ],
            /*
             * Only default service registered
             */
            [
                'servicesToBeRegistered' => $onlyDefaultService,
                'getAlias'               => 'alias1',
                'expectServiceID'        => 'taggedServiceID1',
                'expectedService'        => $onlyDefaultService['alias1']['serviceMock'],
                'message'                => 'Service registered with \'alias1\' should be returned for \'alias1\'',
            ],
            [
                'servicesToBeRegistered' => $onlyDefaultService,
                'getAlias'               => 'not-registered-alias',
                'expectServiceID'        => 'taggedServiceID1',
                'expectedService'        => $onlyDefaultService['alias1']['serviceMock'],
                'message'                => 'Default service should be returned if using non-existing-alias',
            ],
        ];
    }

    /**
     * @param array $registrationArgs
     */
    private function registerServices(array $registrationArgs)
    {
        $this->serviceContainer = $this->mockServiceContainer($registrationArgs);
        $this->serviceProvider->setContainer($this->serviceContainer);
        foreach ($registrationArgs as $serviceArguments) {
            $alias = $serviceArguments['alias'];
            $serviceId = $serviceArguments['serviceID'];
            $isDefault = $serviceArguments['default'];
            $this->serviceProvider->register($alias, $serviceId, $isDefault);
        }
    }

    /**
     * @param array $registrationArgs
     *
     * @return MockInterface
     */
    private function mockServiceContainer(array $registrationArgs)
    {
        $serviceContainer = Mockery::mock(ContainerInterface::class);
        foreach ($registrationArgs as $serviceArguments) {
            $serviceId = $serviceArguments['serviceID'];
            $service = $serviceArguments['serviceMock'];
            $serviceContainer->shouldReceive('get')->with($serviceId)->andReturn($service);
        }

        return $serviceContainer;
    }
}
