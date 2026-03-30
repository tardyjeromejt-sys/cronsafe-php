<?php

namespace CronSafe;

/**
 * Send a success ping to a CronSafe monitor.
 *
 * @param string $slug Monitor slug from your CronSafe dashboard.
 * @param string|null $output Optional text output.
 * @return bool True if accepted.
 */
function ping(string $slug, ?string $output = null): bool
{
    static $client = null;
    if ($client === null) {
        $client = new CronSafe();
    }
    return $client->ping($slug, null, $output);
}

/**
 * Signal the start of a job.
 *
 * @param string $slug Monitor slug.
 * @return bool True if accepted.
 */
function ping_start(string $slug): bool
{
    static $client = null;
    if ($client === null) {
        $client = new CronSafe();
    }
    return $client->pingStart($slug);
}

/**
 * Signal a job failure.
 *
 * @param string $slug Monitor slug.
 * @param string|null $output Optional error message.
 * @return bool True if accepted.
 */
function ping_fail(string $slug, ?string $output = null): bool
{
    static $client = null;
    if ($client === null) {
        $client = new CronSafe();
    }
    return $client->pingFail($slug, $output);
}
