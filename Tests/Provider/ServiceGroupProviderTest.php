<?php
namespace Msales\GrapesBundle\Tests\Provider;

use Msales\OptimizerBundle\Tests\Base\BaseTestCase;
use Msales\GrapesBundle\Exception\AliasNotRegisteredException;
use Msales\GrapesBundle\Provider\ServiceGroupProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mockery;
use stdClass;
use Mockery\MockInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ServiceGroupProviderTest extends BaseTestCase
{
    const SERVICE_ID = 'serviceID';

    /** @var ContainerInterface|MockInterface */
    private $serviceContainer;

    /** @var ServiceGroupProvider */
    private $serviceGroupProvider;

    public function setUp()
    {
        $this->serviceGroupProvider = new ServiceGroupProvider();
    }

    /**
     * @test
     *
     * @dataProvider registerServicesProvider
     *
     * @param array           $servicesToBeRegistered Arguments to register services without a default service
     * @param string          $getAlias
     * @param stdClass[]|null $expectedServiceMocks
     * @param string          $message
     */
    public function getServiceFromProviderByAlias(
        array $servicesToBeRegistered,
        $getAlias,
        $expectedServiceMocks,
        $message
    ) {
        $this->registerServices($servicesToBeRegistered);

        $requestingKnownService = !is_null($expectedServiceMocks);
        if (!$requestingKnownService) {
            /*
             * We need to check both types of exception.
             */
            $this->expectException(ServiceNotFoundException::class);
            $this->expectException(AliasNotRegisteredException::class);
        }

        $services = $this->serviceGroupProvider->get($getAlias);

        if ($requestingKnownService) {
            $this->assertSame($expectedServiceMocks, $services, $message);
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
        $service4 = json_decode('{"name": "pass-through Service1 mock"}');
        $service5 = json_decode('{"name": "pass-through Service2 mock"}');
        $service6 = json_decode('{"name": "pass-through Service3 mock"}');

        $servicesToBeRegisteredWithoutDefault = [
            'groupsWithoutDefault' => [
                [
                    'groupAlias'     => 'group1',
                    self::SERVICE_ID => 'taggedServiceID1',
                    'default'        => false,
                    'serviceMock'    => $service1,
                ],
                [
                    'groupAlias'     => 'group1',
                    self::SERVICE_ID => 'taggedServiceID2',
                    'default'        => false,
                    'serviceMock'    => $service2,
                ],
                [
                    'groupAlias'     => 'group1',
                    self::SERVICE_ID => 'taggedServiceID3',
                    'default'        => false,
                    'serviceMock'    => $service3,
                ],
                [
                    'groupAlias'     => 'group2',
                    self::SERVICE_ID => 'taggedServiceID4',
                    'default'        => false,
                    'serviceMock'    => $service4,
                ],
                [
                    'groupAlias'     => 'group2',
                    self::SERVICE_ID => 'taggedServiceID5',
                    'default'        => false,
                    'serviceMock'    => $service5,
                ],
                [
                    'groupAlias'     => 'group2',
                    self::SERVICE_ID => 'taggedServiceID6',
                    'default'        => false,
                    'serviceMock'    => $service6,
                ],
            ],
        ];

        $groupsWithoutDefault = [
            'group1' => [
                'servicesInGroup' => [
                    $service1,
                    $service2,
                    $service3,
                ],
            ],
            'group2' => [
                'servicesInGroup' => [
                    $service4,
                    $service5,
                    $service6,
                ],
            ],
        ];

        $servicesToBeRegisteredWithDefault = [
            'groupsWithDefault' => [
                [
                    'groupAlias'     => 'group1',
                    self::SERVICE_ID => 'taggedServiceID1',
                    'default'        => true,
                    'serviceMock'    => $service1,
                ],
                [
                    'groupAlias'     => 'group1',
                    self::SERVICE_ID => 'taggedServiceID2',
                    'default'        => true,
                    'serviceMock'    => $service2,
                ],
                [
                    'groupAlias'     => 'group1',
                    self::SERVICE_ID => 'taggedServiceID3',
                    'default'        => false,
                    'serviceMock'    => $service3,
                ],
                [
                    'groupAlias'     => 'group2',
                    self::SERVICE_ID => 'taggedServiceID4',
                    'default'        => true,
                    'serviceMock'    => $service4,
                ],
                [
                    'groupAlias'     => 'group2',
                    self::SERVICE_ID => 'taggedServiceID5',
                    'default'        => true,
                    'serviceMock'    => $service5,
                ],
                [
                    'groupAlias'     => 'group2',
                    self::SERVICE_ID => 'taggedServiceID6',
                    'default'        => false,
                    'serviceMock'    => $service6,
                ],
            ],
        ];

        $groupsWithDefault = [
            'group1'          => [
                'servicesInGroup' => [
                    $service1,
                    $service2,
                    $service3,
                ],
            ],
            'group2'          => [
                'servicesInGroup' => [
                    $service4,
                    $service5,
                    $service6,
                ],
            ],
            'defaultServices' => [
                $service1,
                $service2,
                $service4,
                $service5,
            ],
        ];

        $onlyDefaultService = [
            'groupsWithOnlyDefault' => [
                [
                    'groupAlias'     => 'group1',
                    self::SERVICE_ID => 'taggedServiceID1',
                    'default'        => true,
                    'serviceMock'    => $service1,
                ],
                [
                    'groupAlias'     => 'group1',
                    self::SERVICE_ID => 'taggedServiceID2',
                    'default'        => true,
                    'serviceMock'    => $service2,
                ],
                [
                    'groupAlias'     => 'group1',
                    self::SERVICE_ID => 'taggedServiceID3',
                    'default'        => true,
                    'serviceMock'    => $service3,
                ],
                [
                    'groupAlias'     => 'group2',
                    self::SERVICE_ID => 'taggedServiceID4',
                    'default'        => true,
                    'serviceMock'    => $service4,
                ],
                [
                    'groupAlias'     => 'group2',
                    self::SERVICE_ID => 'taggedServiceID5',
                    'default'        => true,
                    'serviceMock'    => $service5,
                ],
                [
                    'groupAlias'     => 'group2',
                    self::SERVICE_ID => 'taggedServiceID6',
                    'default'        => true,
                    'serviceMock'    => $service6,
                ],
            ],
        ];

        $groupsWithOnlyDefault = [
            'group1'          => [
                'servicesInGroup' => [
                    $service1,
                    $service2,
                    $service3,
                ],
            ],
            'group2'          => [
                'servicesInGroup' => [
                    $service4,
                    $service5,
                    $service6,
                ],
            ],
            'defaultServices' => [
                $service1,
                $service2,
                $service3,
                $service4,
                $service5,
                $service6,
            ],
        ];

        return [
            /*
             * Testing without default
             */
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithoutDefault,
                'getAlias'               => 'group1',
                'expectedServices'       => $groupsWithoutDefault['group1']['servicesInGroup'],
                'message'                => 'Services registered in \'group1\' should be returned for \'group1\' alias',
            ],
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithoutDefault,
                'getAlias'               => 'group2',
                'expectedServices'       => $groupsWithoutDefault['group2']['servicesInGroup'],
                'message'                => 'Services registered in \'group2\' should be returned for \'group2\' alias',
            ],
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithoutDefault,
                'getAlias'               => 'not-existing-alias',
                'expectedServices'       => null,
                'message'                => '',
            ],
            /*
             * Testing with default
             */
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithDefault,
                'getAlias'               => 'group1',
                'expectedServices'       => $groupsWithDefault['group1']['servicesInGroup'],
                'message'                => 'Services registered in \'group1\' should be returned for \'group1\' alias',
            ],
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithDefault,
                'getAlias'               => 'group2',
                'expectedServices'       => $groupsWithDefault['group2']['servicesInGroup'],
                'message'                => 'Services registered in \'group2\' should be returned for \'group2\' alias',
            ],
            [
                'servicesToBeRegistered' => $servicesToBeRegisteredWithDefault,
                'getAlias'               => 'not-existing-alias',
                'expectedServices'       => $groupsWithDefault['defaultServices'],
                'message'                => 'Default services should be returned if using non-existing-alias',
            ],
            /*
             * With no service registered
             */
            [
                'servicesToBeRegistered' => [],
                'getAlias'               => 'not-existing-alias',
                'expectedServices'       => null,
                'message'                => '',
            ],
            /*
             * Only default service registered
             */
            [
                'servicesToBeRegistered' => $onlyDefaultService,
                'getAlias'               => 'group1',
                'expectedServices'       => $groupsWithOnlyDefault['group1']['servicesInGroup'],
                'message'                => 'Services registered in \'group1\' should be returned for \'group1\' alias',
            ],
            [
                'servicesToBeRegistered' => $onlyDefaultService,
                'getAlias'               => 'group2',
                'expectedServices'       => $groupsWithOnlyDefault['group2']['servicesInGroup'],
                'message'                => 'Services registered in \'group2\' should be returned for \'group2\' alias',
            ],
            [
                'servicesToBeRegistered' => $onlyDefaultService,
                'getAlias'               => 'not-registered-alias',
                'expectedServices'       => $groupsWithOnlyDefault['defaultServices'],
                'message'                => 'Default services should be returned if using non-existing-alias',
            ],
        ];
    }

    /**
     * @param array $registrationArgs
     */
    private function registerServices(array $registrationArgs)
    {
        $this->serviceContainer = $this->mockServiceContainer($registrationArgs);
        $this->serviceGroupProvider->setContainer($this->serviceContainer);

        foreach ($registrationArgs as $serviceArguments) {
            for ($i = 0; $i < count($serviceArguments); ++$i) {
                $alias = $serviceArguments[$i]['groupAlias'];
                $serviceID = $serviceArguments[$i][self::SERVICE_ID];
                $isDefault = $serviceArguments[$i]['default'];
                $this->serviceGroupProvider->register($alias, $serviceID, $isDefault);
            }
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
            for ($i = 0; $i < count($serviceArguments); ++$i) {
                $serviceId = $serviceArguments[$i][self::SERVICE_ID];
                $service = $serviceArguments[$i]['serviceMock'];
                $serviceContainer->shouldReceive('get')->with($serviceId)->andReturn($service);
            }
        }

        return $serviceContainer;
    }
}
