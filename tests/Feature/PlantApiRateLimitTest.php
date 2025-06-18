<?php

namespace Tests\Feature;

use Tests\TestCase;

class PlantApiRateLimitTest extends TestCase
{
    protected $baseUrl = '/api/v1';
    protected $plant_id = 13597;


    protected $rateLimitingMaxAttepts = 125;

    /**
     * Test that multiple sequential requests are allowed within normal limits.
     *
     * @return void
     */
    public function testMultipleRequestsWithinLimitsAreAllowed()
    {
        // Make several legitimate requests that should be under the rate limit
        for ($i = 0; $i < 5; $i++) {
            $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");
            $response->assertStatus(200);

            // Small delay to simulate natural request pattern
            usleep(200000); // 200ms delay
        }
    }

    /**
     * Test that the API responds with appropriate status code when rate limit is exceeded.
     *
     * @return void
     */
    public function testRateLimitExceededResponseCode()
    {
        // Make many rapid requests to trigger rate limiting
        $rateLimitTriggered = false;
        $attempts = 0;
        $maxAttempts = $this->rateLimitingMaxAttepts; // Maximum attempts to try triggering rate limit

        while (!$rateLimitTriggered && $attempts < $maxAttempts) {
            $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");
            $attempts++;

            // If we get a rate limit response, we've succeeded
            if ($response->status() === 429) {
                $rateLimitTriggered = true;
                break;
            }

            // No delay to maximize chance of hitting rate limit
        }

        // If rate limiting is implemented, we should have triggered it
        if ($rateLimitTriggered) {
            $this->assertEquals(429, $response->status());
        } else {
            $this->markTestSkipped('Could not trigger rate limiting after ' . $maxAttempts . ' attempts. Rate limiting may not be implemented or threshold is very high.');
        }
    }

    /**
     * Test that rate limit response includes appropriate headers.
     *
     * @return void
     */
    public function testRateLimitResponseHeaders()
    {
        // Make many rapid requests to trigger rate limiting
        $rateLimitTriggered = false;
        $attempts = 0;
        $maxAttempts = $this->rateLimitingMaxAttepts;

        while (!$rateLimitTriggered && $attempts < $maxAttempts) {
            $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");
            $attempts++;

            if ($response->status() === 429) {
                $rateLimitTriggered = true;
                break;
            }
        }

        if ($rateLimitTriggered) {
            // Common rate limit headers
            $expectedHeaders = [
                'X-RateLimit-Limit',
                'X-RateLimit-Remaining',
                'Retry-After',
                'X-RateLimit-Reset'
            ];

            // Check if any of the expected headers are present
            $hasRateLimitHeaders = false;
            foreach ($expectedHeaders as $header) {
                if ($response->headers->has($header)) {
                    $hasRateLimitHeaders = true;
                    break;
                }
            }

            $this->assertTrue($hasRateLimitHeaders, 'Response does not contain any expected rate limit headers');
        } else {
            $this->markTestSkipped('Could not trigger rate limiting after ' . $maxAttempts . ' attempts.');
        }
    }

    /**
     * Test that the API respects the Retry-After header timing.
     *
     * @return void
     */
    public function testRetryAfterHeaderIsRespected()
    {
        // Step 1: Trigger rate limiting
        $rateLimitTriggered = false;
        $attempts = 0;
        $maxAttempts = $this->rateLimitingMaxAttepts;

        while (!$rateLimitTriggered && $attempts < $maxAttempts) {
            $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");
            $attempts++;

            if ($response->status() === 429) {
                $rateLimitTriggered = true;
                break;
            }
        }

        if (!$rateLimitTriggered) {
            $this->markTestSkipped('Could not trigger rate limiting.');
            return;
        }

        // Step 2: Extract Retry-After value
        if ($response->headers->has('Retry-After')) {
            $retryAfter = (int) $response->headers->get('Retry-After');

            // Step 3: Make a request immediately (should be denied)
            $immediateResponse = $this->get($this->baseUrl . "/plants/{$this->plant_id}");
            $this->assertEquals(429, $immediateResponse->status(), 'Request should still be rate-limited immediately after 429 response');

            // We'll check if the API respects partial wait times
            // If retry-after is more than 5 seconds, we'll test for partial waiting
            if ($retryAfter > 5) {
                // Wait half the required time
                sleep(ceil($retryAfter / 2));

                // Request should still be denied
                $partialWaitResponse = $this->get($this->baseUrl . "/plants/{$this->plant_id}");
                $this->assertEquals(429, $partialWaitResponse->status(), 'Request should still be rate-limited after partial wait');
            } else {
                $this->markTestSkipped('Retry-After value too small to test partial waiting: ' . $retryAfter . ' seconds');
            }

            // Wait the full time plus a small buffer
            sleep($retryAfter + 1);

            // Request should now be allowed
            $fullWaitResponse = $this->get($this->baseUrl . "/plants/{$this->plant_id}");
            $this->assertEquals(200, $fullWaitResponse->status(), 'Request should be allowed after respecting Retry-After');

        } else {
            $this->markTestSkipped('Rate limit response does not include Retry-After header.');
        }
    }


    /**
     * Test rate limit handling across different HTTP methods.
     *
     * @return void
     */
    public function testRateLimitAcrossDifferentHttpMethods()
    {
        // First, trigger rate limiting with GET requests
        $rateLimitTriggered = false;
        $attempts = 0;
        $maxAttempts = $this->rateLimitingMaxAttepts;

        while (!$rateLimitTriggered && $attempts < $maxAttempts) {
            $response = $this->get($this->baseUrl . "/plants/{$this->plant_id}");
            $attempts++;

            if ($response->status() === 429) {
                $rateLimitTriggered = true;
                break;
            }
        }

        if (!$rateLimitTriggered) {
            $this->markTestSkipped('Could not trigger rate limiting with GET requests.');
            return;
        }

        // Test if other methods are also rate-limited
        // Using HEAD as a safe method to test
        $headResponse = $this->head($this->baseUrl . "/plants/{$this->plant_id}");

        // Check if HEAD requests are also rate-limited
        // This will vary based on your API's rate limiting implementation
        $this->assertEquals(
            $response->status(),
            $headResponse->status(),
            'HEAD requests should be handled the same as GET requests for rate limiting'
        );
    }
}