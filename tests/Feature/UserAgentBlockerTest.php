<?php

namespace Webdevartisan\LaravelBlocker\Tests\Feature;

use Webdevartisan\LaravelBlocker\Exceptions\MaliciousUserAgentException;
use Webdevartisan\LaravelBlocker\Facades\BlockedIpStore;
use Webdevartisan\LaravelBlocker\Http\Middleware\BlockMaliciousUsers;
use Webdevartisan\LaravelBlocker\Facades\LaravelBlocker;
use Webdevartisan\LaravelBlocker\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

/**
 * Class LogCleanerTest
 */
class UserAgentBlockerTest extends TestCase
{

    use RefreshDatabase;

    const HOST = 'https://example.com';
    const IP_ADDRESS = '123.456.78.90';

    /** @test */
    public function itDeterminesUserAgentFromRequest()
    {
        $this->assertSame('Symfony', LaravelBlocker::getUserAgent());
    }

    /** @test */
    public function itDeterminesMaliciousUserAgent()
    {
        config(['laravel-shield.malicious_user_agents' => 'symfony']);
        $this->assertSame(true, LaravelBlocker::isMaliciousUserAgent());

        config(['laravel-shield.malicious_user_agents' => 'GoogleBot|BingBot']);
        $this->assertSame(false, LaravelBlocker::isMaliciousUserAgent());
    }

    /** @test */
    public function middlewareStoresIpOnMaliciousUserAgent()
    {
        config(['laravel-shield.malicious_user_agents' => 'symfony']);
        $this->get(self::HOST);
        $request = new Request();
        request()->server->add(['REMOTE_ADDR' => self::IP_ADDRESS]);

        $this->expectException(MaliciousUserAgentException::class);

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
        config(['laravel-shield.malicious_user_agents' => 'symfony']);
        $this->get(self::HOST);
        $request = new Request();

        (new BlockMaliciousUsers())->handle($request, function ($request) {
            $this->assertSame(false, BlockedIpStore::has(self::IP_ADDRESS));
        });
    }
}
