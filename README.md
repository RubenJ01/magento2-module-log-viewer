# Magento 2 Log Viewer

Version Packagist Downloads License

A Magento 2 module to view and manage log files directly from the admin panel.

## Installation

Install from Packagist:

```bash
composer require rjds/magento2-module-log-viewer
bin/magento module:enable Rjds_LogViewer
bin/magento setup:upgrade
bin/magento cache:flush
```

If you use a private Composer mirror, add your repository configuration first and then run the same require command.

## Overview

View Magento logs from the backend without SSH or filesystem access.
The module provides an admin interface to:

- list available log files
- inspect log contents
- filter and search entries
- safely clear log files when needed

## Quick Start

1. Go to **Magento Admin**.
2. Navigate to **System > Log Viewer**.
3. Select a log file (for example `system.log`, `exception.log`, or `debug.log`).
4. Review entries and apply filters/search as needed.

### Configure permissions

Grant access to the Log Viewer in **System > Permissions > User Roles**.

Make sure administrators who need this feature have the appropriate ACL resource enabled.

### Typical workflow

- Open `exception.log` after a reported checkout issue
- Filter by timestamp and keyword (order increment, customer email, etc.)
- Validate stack traces and correlate with deployment/release time
- Clear or archive logs after issue resolution (according to your internal policy)

## Features

- Admin-based log browsing
- Supports common Magento log files
- Search/filter support for faster troubleshooting
- Role-based access control (ACL)
- Magento-native module structure and integration

## Development

```bash
composer install
```

For local development in Magento:

```bash
bin/magento setup:upgrade
bin/magento cache:flush
```

Run your project quality checks (PHPStan, PHPCS, PHPUnit, etc.) according to your repository setup.

## Contributing

Contributions are welcome.
Please open an issue or pull request with a clear description, reproduction steps (if bug-related), and expected behavior.

## Migrations

If future major versions introduce breaking changes, migration notes will be documented in `MIGRATION.md`.

## License

This project is released under the MIT License.
See `LICENSE` for details and `CHANGELOG.md` for release history.
