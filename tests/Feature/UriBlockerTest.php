<?php

namespace Webdevartisan\LaravelBlocker\Tests\Feature;

use Webdevartisan\LaravelBlocker\Facades\BlockedIpStore;
use Webdevartisan\LaravelBlocker\Facades\LaravelBlocker;
use Webdevartisan\LaravelBlocker\Http\Middleware\BlockMaliciousUsers;
use Webdevartisan\LaravelBlocker\Tests\TestCase;
use Illuminate\Http\Request;

/**
 * Class LogCleanerTest
 */
class UriBlockerTest extends TestCase
{
    const HOST = 'https://example.com';
    const IP_ADDRESS = '123.456.78.90';

    /** @test */
    public function itDeterminesAMaliciousUrlFromAString()
    {
        config(['laravel-shield.malicious_urls' => ['call_user_func_array']]);
        $this->assertSame(true, LaravelBlocker::isMaliciousUri(self::HOST . '/?invokefunction&function=call_user_func_array&vars[0]=phpinfo'));
    }

    /** @test */
    public function itEscapesRegexCharacters()
    {
        config(['laravel-shield.malicious_urls' => ['wp-admin']]);
        $this->assertSame(true, LaravelBlocker::isMaliciousUri(self::HOST . '/wp-admin/'));

        config(['laravel-shield.malicious_urls' => ['?foo']]);
        $this->assertSame(true, LaravelBlocker::isMaliciousUri(self::HOST . '/?foo=bar'));

        config(['laravel-shield.malicious_urls' => ['.git']]);
        $this->assertSame(true, LaravelBlocker::isMaliciousUri(self::HOST . '/.git'));
    }

    /** @test */
    public function itDeterminesMaliciousUrlFromRequest()
    {
        // Request a malicious URL
        $this->mockMaliciousUrlInRequest();
        $this->assertSame(true, LaravelBlocker::isMaliciousRequest());
    }

    /** @test */
    public function middlewareThrowsExceptionOnMaliciousUrl()
    {
        // Request a malicious URL
        $this->mockMaliciousUrlInRequest();
        $request = new Request();
        $request->merge(['ip' => self::IP_ADDRESS]);

        $this->expectExceptionCode(406);


        (new BlockMaliciousUsers())->handle($request, function ($request) {
            // Malicious IP has been stored
            $this->assertSame(true, BlockedIpStore::has(self::IP_ADDRESS));
        });
    }

    /** @test */
    public function middlewareThrowsExceptionOnBlockedIp()
    {
        // Store a blocked IP
        BlockedIpStore::create(self::IP_ADDRESS);

        // Do a request as the user with the blocked IP
        $this->get('https://test.domain.com/');
        request()->server->add(['REMOTE_ADDR' => self::IP_ADDRESS]);

        $this->expectExceptionCode(401);

        (new BlockMaliciousUsers())->handle(request(), function ($request) {
            $this->assertSame(true, BlockedIpStore::has(self::IP_ADDRESS));
        });
    }

    /** @test */
    public function middlewareDeletesExpiredBlockedIp()
    {
        // Create a blocked user whose block has expired
        BlockedIpStore::create(self::IP_ADDRESS, -10);

        // Do a request as the user with the blocked IP, but not a malicious URL
        $this->get('https://test.domain.com/');
        request()->server->add(['REMOTE_ADDR' => self::IP_ADDRESS]);

        // The blocked user should have been deleted and no exception should have been thrown
        (new BlockMaliciousUsers())->handle(request(), function ($request) {
            $this->assertSame(false, BlockedIpStore::has(self::IP_ADDRESS));
        });
    }

    /** @test */
    public function MaliciousUrlDetectionCanBeDisabled()
    {
        // Disable url_detection_enabled
        config(['laravel-shield.url_detection_enabled' => false]);
        $this->mockMaliciousUrlInRequest();

        $request = new Request();
        $request->merge(['ip' => [self::IP_ADDRESS]]);
        (new BlockMaliciousUsers())->handle($request, function ($request) {
            $this->assertSame(false, BlockedIpStore::has(self::IP_ADDRESS));
        });
    }

    /**
     * @return void
     */
    protected function mockMaliciousUrlInRequest(): void
    {
        config(['laravel-shield.malicious_urls' => ['wp-admin']]);
        $this->get('https://test.domain.com/wp-admin');
        request()->server->add(['REMOTE_ADDR' => self::IP_ADDRESS]);
    }

}
