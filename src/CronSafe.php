<?php

namespace CronSafe;

/**
 * CronSafe client for cron job monitoring.
 *
 * Usage:
 *   $client = new \CronSafe\CronSafe();
 *   $client->ping('your-monitor-slug');
 *   $client->pingStart('your-monitor-slug');
 *   $client->pingFail('your-monitor-slug', 'error message');
 */
class CronSafe
{
    private string $baseUrl;
    private int $timeout;

    /**
     * @param string $baseUrl API base URL (default: https://api.getcronsafe.com)
     * @param int $timeout Request timeout in seconds (default: 10)
     */
    public function __construct(
        string $baseUrl = 'https://api.getcronsafe.com',
        int $timeout = 10
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }

    /**
     * Send a ping to a CronSafe monitor.
     *
     * @param string $slug Monitor slug from your CronSafe dashboard.
     * @param string|null $status Optional status ("fail" to signal failure).
     * @param string|null $output Optional text output (e.g., stderr, logs).
     * @return bool True if the ping was accepted (HTTP 2xx).
     * @throws CronSafeException On network or server errors.
     */
    public function ping(string $slug, ?string $status = null, ?string $output = null): bool
    {
        $url = $this->baseUrl . '/ping/' . urlencode($slug);

        $params = [];
        if ($status !== null) {
            $params['status'] = $status;
        }
        if ($output !== null) {
            $params['output'] = $output;
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $this->request($url);
    }

    /**
     * Signal the start of a job (for duration tracking).
     *
     * @param string $slug Monitor slug.
     * @return bool True if accepted.
     * @throws CronSafeException On network errors.
     */
    public function pingStart(string $slug): bool
    {
        $url = $this->baseUrl . '/ping/' . urlencode($slug) . '/start';
        return $this->request($url);
    }

    /**
     * Signal a job failure. Triggers alerts immediately.
     *
     * @param string $slug Monitor slug.
     * @param string|null $output Optional error message.
     * @return bool True if accepted.
     * @throws CronSafeException On network errors.
     */
    public function pingFail(string $slug, ?string $output = null): bool
    {
        return $this->ping($slug, 'fail', $output);
    }

    /**
     * @throws CronSafeException
     */
    private function request(string $url): bool
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $this->timeout,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new CronSafeException('Connection error: unable to reach ' . $this->baseUrl);
        }

        // Parse HTTP status from response headers
        $statusCode = 0;
        if (isset($http_response_header[0])) {
            preg_match('/\d{3}/', $http_response_header[0], $matches);
            if (!empty($matches)) {
                $statusCode = (int) $matches[0];
            }
        }

        if ($statusCode === 404) {
            return false;
        }

        if ($statusCode >= 400) {
            throw new CronSafeException("HTTP {$statusCode} error");
        }

        return $statusCode >= 200 && $statusCode < 300;
    }
}
