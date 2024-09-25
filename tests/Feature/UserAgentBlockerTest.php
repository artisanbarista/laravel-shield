<?php

namespace Artisanbarista\LaravelShield\Tests\Feature;

use Artisanbarista\LaravelShield\Facades\BlockedIpStore;
use Artisanbarista\LaravelShield\Http\Middleware\BlockMaliciousUsers;
use Artisanbarista\LaravelShield\Facades\LaravelShield;
use Artisanbarista\LaravelShield\Tests\TestCase;
use Illuminate\Http\Request;

/**
 * Class LogCleanerTest
 */
class UserAgentBlockerTest extends TestCase
{
    const HOST = 'https://example.com';
    const IP_ADDRESS = '123.456.78.90';

    /** @test */

    /** @test */
    public function itDeterminesMaliciousUserAgent()
    {
        config(['laravel-shield.malicious_user_agents' => ['symfony']]);
        $this->assertSame(true, LaravelShield::isMaliciousUserAgent(request()->header('user-agent')));

        config(['laravel-shield.malicious_user_agents' => ['GoogleBot','BingBot']]);
        $this->assertSame(false, LaravelShield::isMaliciousUserAgent(request()->header('user-agent')));
    }

    /** @test */
    public function middlewareStoresIpOnMaliciousUserAgent()
    {
        config(['laravel-shield.malicious_user_agents' => ['symfony']]);
        $this->get(self::HOST);
        $request = new Request();
        request()->server->add(['REMOTE_ADDR' => self::IP_ADDRESS]);

        $this->expectExceptionCode(406);

        (new BlockMaliciousUsers())->handle($request, function ($request) {
            $this->assertSame(true, BlockedIpStore::has(self::IP_ADDRESS));
        });
    }

    /** @test */
    public function MaliciousUserAgentlDetectionCanBeDisabled()
    {
        // Disable user_agent_detection_enabled
        config(['laravel-shield.user_agent_detection_enabled' => false]);
        // Request a malicious User Agent
        config(['laravel-shield.malicious_user_agents' => ['symfony']]);
        $this->get(self::HOST);
        $request = new Request();

        (new BlockMaliciousUsers())->handle($request, function ($request) {
            $this->assertSame(false, BlockedIpStore::has(self::IP_ADDRESS));
        });
    }
}
