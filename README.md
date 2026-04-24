# Magento 2 Log Viewer

[![Version](https://img.shields.io/packagist/v/rjds/magento2-module-log-viewer.svg)](https://packagist.org/packages/rjds/magento2-module-log-viewer)
[![Downloads](https://img.shields.io/packagist/dt/rjds/magento2-module-log-viewer.svg)](https://packagist.org/packages/rjds/magento2-module-log-viewer)
[![License](https://img.shields.io/packagist/l/rjds/magento2-module-log-viewer.svg)](LICENSE)

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

Contributions are welcome. See [`CONTRIBUTING.md`](CONTRIBUTING.md) for contribution guidelines, commit conventions, and pull request workflow.

## Migrations

If future major versions introduce breaking changes, migration notes will be documented in `MIGRATION.md`.

## License

This project is released under the MIT License.
See `LICENSE` for details and `CHANGELOG.md` for release history.
