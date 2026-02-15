<?php

namespace Tests\Unit;

use App\Services\MikrotikService;
use Tests\TestCase;
use RouterOS\Sohag\RouterosAPI;
use Mockery;

class MikrotikServiceTest extends TestCase
{
    protected $mikrotikService;
    protected $routerosApiMock;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a Mockery mock for RouterosAPI
        $this->routerosApiMock = Mockery::mock(RouterosAPI::class);

        // Inject the mock into the MikrotikService
        $this->mikrotikService = new MikrotikService();
        // Access the protected property via reflection to set the mock
        $reflection = new \ReflectionClass($this->mikrotikService);
        $property = $reflection->getProperty('api');
        $property->setAccessible(true);
        $property->setValue($this->mikrotikService, $this->routerosApiMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testAddSimpleQueue()
    {
        $name = 'test-queue';
        $target = '192.168.1.1/32';
        $maxLimit = '1M/1M';
        $comment = 'Test Queue';

        $expectedProperties = [
            'name' => $name,
            'target' => $target,
            'max-limit' => $maxLimit,
            'comment' => $comment,
        ];

        $this->routerosApiMock->shouldReceive('addMktRows')
            ->once()
            ->with('queue_simple', [$expectedProperties])
            ->andReturn(['.id' => '*1']);

        $result = $this->mikrotikService->addSimpleQueue($name, $target, $maxLimit, '', $comment);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('.id', $result);
    }

    public function testUpdateSimpleQueue()
    {
        $name = 'test-queue';
        $properties = ['max-limit' => '2M/2M', 'comment' => 'Updated Queue'];
        $existingQueueId = '*1';

        $this->routerosApiMock->shouldReceive('getMktRows')
            ->once()
            ->with('queue_simple', ['?name' => $name])
            ->andReturn([['.id' => $existingQueueId, 'name' => $name]]);

        $this->routerosApiMock->shouldReceive('ttyWirte')
            ->once()
            ->with('/queue/simple/set', array_merge(['.id' => $existingQueueId], $properties))
            ->andReturn(['!done']);

        $result = $this->mikrotikService->updateSimpleQueue($name, $properties);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertEquals('!done', $result[0]);
    }

    public function testRemoveSimpleQueue()
    {
        $name = 'test-queue';
        $existingQueueId = '*1';

        $this->routerosApiMock->shouldReceive('getMktRows')
            ->once()
            ->with('queue_simple', ['?name' => $name])
            ->andReturn([['.id' => $existingQueueId, 'name' => $name]]);

        $this->routerosApiMock->shouldReceive('ttyWirte')
            ->once()
            ->with('/queue/simple/remove', ['.id' => $existingQueueId])
            ->andReturn(['!done']);

        $result = $this->mikrotikService->removeSimpleQueue($name);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertEquals('!done', $result[0]);
    }

    public function testAddPppSecret()
    {
        $username = 'testuser';
        $password = 'testpass';
        $service = 'pppoe';
        $profile = 'default';
        $comment = 'Test PPP User';

        $expectedProperties = [
            'name' => $username,
            'password' => $password,
            'service' => $service,
            'profile' => $profile,
            'comment' => $comment,
        ];

        $this->routerosApiMock->shouldReceive('addMktRows')
            ->once()
            ->with('ppp_secret', [$expectedProperties])
            ->andReturn(['.id' => '*1']);

        $result = $this->mikrotikService->addPppSecret($username, $password, $service, $profile, '', $comment);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('.id', $result);
    }

    public function testUpdatePppSecret()
    {
        $username = 'testuser';
        $properties = ['password' => 'newpass', 'profile' => 'new_profile'];
        $existingSecretId = '*1';

        $this->routerosApiMock->shouldReceive('getMktRows')
            ->once()
            ->with('ppp_secret', ['?name' => $username])
            ->andReturn([['.id' => $existingSecretId, 'name' => $username]]);

        $this->routerosApiMock->shouldReceive('ttyWirte')
            ->once()
            ->with('/ppp/secret/set', array_merge(['.id' => $existingSecretId], $properties))
            ->andReturn(['!done']);

        $result = $this->mikrotikService->updatePppSecret($username, $properties);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertEquals('!done', $result[0]);
    }

    public function testRemovePppSecret()
    {
        $username = 'testuser';
        $existingSecretId = '*1';

        $this->routerosApiMock->shouldReceive('getMktRows')
            ->once()
            ->with('ppp_secret', ['?name' => $username])
            ->andReturn([['.id' => $existingSecretId, 'name' => $username]]);

        $this->routerosApiMock->shouldReceive('ttyWirte')
            ->once()
            ->with('/ppp/secret/remove', ['.id' => $existingSecretId])
            ->andReturn(['!done']);

        $result = $this->mikrotikService->removePppSecret($username);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertEquals('!done', $result[0]);
    }

    public function testGetRouterBoardResource()
    {
        $expectedResource = [
            ['cpu-load' => '10%', 'free-memory' => '128MiB']
        ];

        $this->routerosApiMock->shouldReceive('getMktRows')
            ->once()
            ->with('system_resource')
            ->andReturn($expectedResource);

        $result = $this->mikrotikService->getRouterBoardResource();

        $this->assertIsArray($result);
        $this->assertEquals($expectedResource, $result);
    }
}