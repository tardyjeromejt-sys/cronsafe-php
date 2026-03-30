# CronSafe PHP SDK

Official PHP SDK for [CronSafe](https://getcronsafe.com) - cron job monitoring with 15 features.

## Installation

```bash
composer require cronsafe/cronsafe-php
```

## Quick Start

```php
<?php
require 'vendor/autoload.php';

// At the end of your cron job:
\CronSafe\ping('your-monitor-slug');
```

## Usage

### Simple ping (job completed successfully)

```php
\CronSafe\ping('nightly-backup');
```

### Signal job start (for duration tracking)

```php
\CronSafe\ping_start('nightly-backup');
// ... your job runs ...
\CronSafe\ping('nightly-backup');
```

### Signal failure

```php
try {
    runBackup();
    \CronSafe\ping('nightly-backup');
} catch (\Exception $e) {
    \CronSafe\ping_fail('nightly-backup', $e->getMessage());
}
```

### Advanced: OOP client

```php
use CronSafe\CronSafe;

$client = new CronSafe(
    baseUrl: 'https://api.getcronsafe.com',  // default
    timeout: 30  // seconds
);

$client->ping('my-monitor');
$client->pingStart('my-monitor');
$client->pingFail('my-monitor', 'disk full');
```

## API Reference

### `\CronSafe\ping(string $slug, ?string $output = null): bool`
Send a success ping. Returns `true` if accepted.

### `\CronSafe\ping_start(string $slug): bool`
Signal job start for duration tracking.

### `\CronSafe\ping_fail(string $slug, ?string $output = null): bool`
Signal failure. Triggers alerts immediately.

### `new CronSafe(string $baseUrl, int $timeout)`
Create a custom client instance.

## Requirements

- PHP >= 7.4
- `allow_url_fopen` enabled (default in most PHP installations)

## Links

- Website: [getcronsafe.com](https://getcronsafe.com)
- Documentation: [getcronsafe.com/docs](https://getcronsafe.com/docs)

## License

MIT
