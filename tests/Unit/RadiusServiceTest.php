<?php

namespace Tests\Unit;

use App\Services\RadiusService;
use Tests\TestCase;
use App\Models\RadCheck;
use App\Models\RadReply;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Mockery;

// Define a global mock for shell_exec for the purpose of these tests
// This function will be defined in the Tests\Unit namespace.
// Its behavior will be controlled by a static property of the RadiusServiceTest class.
// This function must be globally available for shell_exec calls within the separate process.
// We are placing it here, outside the class, to ensure it's loaded before the test class.
if (!function_exists('Tests\Unit\shell_exec')) {
    function shell_exec($command) {
        // This global function will be temporarily overridden by the test methods
        // that run in separate processes. For other tests, it will return this default.
        return ""; // Default empty string to avoid unexpected null
    }
}


class RadiusServiceTest extends TestCase
{
    protected $radiusService;

    // Use a static property to control the return value of the mocked shell_exec
    public static $shellExecReturn;

    protected function setUp(): void
    {
        parent::setUp();
        $this->radiusService = new RadiusService();

        // Reset shell_exec return value for each test that uses the global mock
        self::$shellExecReturn = "rad_send_request: message sent"; // Default to success

        // Mock DB transactions
        DB::shouldReceive('connection')->andReturnSelf();
        DB::shouldReceive('transaction')->andReturnUsing(function ($callback) {
            return $callback();
        });

        // Mock Log
        Log::shouldReceive('info');
        Log::shouldReceive('error');
        Log::shouldReceive('warning');

        // IMPORTANT: Removed alias mocks from setUp to avoid redeclaration issues.
        // Mocks for RadCheck/RadReply static methods will be handled per test method if necessary.
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Clears all mocks
        parent::tearDown();
    }

    public function testAddRadReplyAttribute()
    {
        // Mock the static `create` method of RadReply
        $radReplyMock = Mockery::mock('alias:App\Models\RadReply');
        $radReplyMock->shouldReceive('create')
            ->once()
            ->with([
                'username' => 'testuser',
                'attribute' => 'Mikrotik-Rate-Limit',
                'op' => '=',
                'value' => '1M/1M',
            ])
            ->andReturn(Mockery::mock(RadReply::class)); // Return a mock instance of RadReply

        $result = $this->radiusService->addRadReplyAttribute('testuser', 'Mikrotik-Rate-Limit', '1M/1M', '=');
        $this->assertTrue($result);
    }

    public function testUpdateRadReplyAttribute()
    {
        $username = 'testuser';
        $attribute = 'Mikrotik-Rate-Limit';
        $newValue = '2M/2M';
        $oldValue = '1M/1M';
        $op = '=';

        // Mock the query builder chain
        $mockQueryBuilder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $mockQueryBuilder->shouldReceive('where')->andReturnSelf();
        $mockQueryBuilder->shouldReceive('exists')->andReturn(true); // For the 'exists' check
        $mockQueryBuilder->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                ['username' => $username, 'attribute' => $attribute, 'op' => $op],
                ['value' => $newValue]
            )
            ->andReturn(Mockery::mock(RadReply::class));

        // Mock the static `where` call on RadReply
        $radReplyMock = Mockery::mock('alias:App\Models\RadReply');
        $radReplyMock->shouldReceive('where')->andReturn($mockQueryBuilder);

        $result = $this->radiusService->updateRadReplyAttribute($username, $attribute, $newValue, $oldValue, $op);
        $this->assertTrue($result);
    }

    public function testRemoveRadReplyAttribute()
    {
        $username = 'testuser';
        $attribute = 'Mikrotik-Rate-Limit';
        $value = '1M/1M';
        $op = '=';

        // Mock the query builder chain
        $mockQueryBuilder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $mockQueryBuilder->shouldReceive('where')->andReturnSelf();
        $mockQueryBuilder->shouldReceive('delete')->once()->andReturn(1); // Simulate successful deletion

        // Mock the static `where` call on RadReply
        $radReplyMock = Mockery::mock('alias:App\Models\RadReply');
        $radReplyMock->shouldReceive('where')->andReturn($mockQueryBuilder);

        $result = $this->radiusService->removeRadReplyAttribute($username, $attribute, $value, $op);
        $this->assertTrue($result);
    }

    public function testAddRadCheckAttribute()
    {
        // Mock the static `create` method of RadCheck
        $radCheckMock = Mockery::mock('alias:App\Models\RadCheck');
        $radCheckMock->shouldReceive('create')
            ->once()
            ->with([
                'username' => 'testuser',
                'attribute' => 'Calling-Station-Id',
                'op' => '==',
                'value' => 'AA-BB-CC-DD-EE-FF',
            ])
            ->andReturn(Mockery::mock(RadCheck::class));

        $result = $this->radiusService->addRadCheckAttribute('testuser', 'Calling-Station-Id', 'AA-BB-CC-DD-EE-FF', '==');
        $this->assertTrue($result);
    }

    public function testUpdateRadCheckAttribute()
    {
        $username = 'testuser';
        $attribute = 'Calling-Station-Id';
        $newValue = 'FF-EE-DD-CC-BB-AA';
        $oldValue = 'AA-BB-CC-DD-EE-FF';
        $op = '==';

        // Mock the query builder chain
        $mockQueryBuilder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $mockQueryBuilder->shouldReceive('where')->andReturnSelf();
        $mockQueryBuilder->shouldReceive('exists')->andReturn(true); // For the 'exists' check
        $mockQueryBuilder->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                ['username' => $username, 'attribute' => $attribute, 'op' => $op],
                ['value' => $newValue]
            )
            ->andReturn(Mockery::mock(RadCheck::class));

        // Mock the static `where` call on RadCheck
        $radCheckMock = Mockery::mock('alias:App\Models\RadCheck');
        $radCheckMock->shouldReceive('where')->andReturn($mockQueryBuilder);

        $result = $this->radiusService->updateRadCheckAttribute($username, $attribute, $newValue, $oldValue, $op);
        $this->assertTrue($result);
    }

    public function testRemoveRadCheckAttribute()
    {
        $username = 'testuser';
        $attribute = 'Calling-Station-Id';
        $value = 'AA-BB-CC-DD-EE-FF';
        $op = '==';

        // Mock the query builder chain
        $mockQueryBuilder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        $mockQueryBuilder->shouldReceive('where')->andReturnSelf();
        $mockQueryBuilder->shouldReceive('delete')->once()->andReturn(1); // Simulate successful deletion

        // Mock the static `where` call on RadCheck
        $radCheckMock = Mockery::mock('alias:App\Models\RadCheck');
        $radCheckMock->shouldReceive('where')->andReturn($mockQueryBuilder);

        $result = $this->radiusService->removeRadCheckAttribute($username, $attribute, $value, $op);
        $this->assertTrue($result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState false
     */
    public function testDisconnectUserSuccess()
    {
        $username = 'testuser';
        $nasIpAddress = '10.0.0.1';

        Config::shouldReceive('get')->with('radius.coa.ip', '127.0.0.1')->andReturn('127.0.0.1');
        Config::shouldReceive('get')->with('radius.coa.port', '3799')->andReturn('3799');
        Config::shouldReceive('get')->with('radius.coa.secret', 'secret')->andReturn('testsecret');

        // Temporarily redefine shell_exec for this isolated process
        // This will override the global shell_exec defined in the Tests\Unit namespace
        function shell_exec($command) {
            return "rad_send_request: message sent"; // Simulate success
        }

        $result = $this->radiusService->disconnectUser($username, $nasIpAddress);
        $this->assertTrue($result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState false
     */
    public function testDisconnectUserFailure()
    {
        $username = 'testuser';
        $nasIpAddress = '10.0.0.1';

        Config::shouldReceive('get')->with('radius.coa.ip', '127.0.0.1')->andReturn('127.0.0.1');
        Config::shouldReceive('get')->with('radius.coa.port', '3799')->andReturn('3799');
        Config::shouldReceive('get')->with('radius.coa.secret', 'secret')->andReturn('testsecret');

        // Temporarily redefine shell_exec for this isolated process
        // This will override the global shell_exec defined in the Tests\Unit namespace
        function shell_exec($command) {
            return "Error: failed to send packet"; // Simulate failure
        }

        $result = $this->radiusService->disconnectUser($username, $nasIpAddress);
        $this->assertFalse($result);
    }
}