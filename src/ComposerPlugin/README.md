# OpenTelemetry Composer Plugin

## Purpose

This plugin exists to complement the functionality that the SPI package provides.

## Usage

Packages in the OpenTelemetry ecosystem have typically registered themselves via the Composer autoload files directive:

```json
{
    "autoload": {
        "files": [
            "_register.php"
        ]
    }
}
```

We can register with SPI explicitly in case the SPI plugin has not been allowed.

This would typically be something similar to the below:
```php
<?php

declare(strict_types=1);

use Nevay\SPI\ServiceLoader;
use OpenTelemetry\API\Instrumentation\AutoInstrumentation\Instrumentation;

ServiceLoader::register(Instrumentation::class, \My\App\Custom\Instrumentation::class);
```

When the SPI plugin is allowed to run, the above file can be rendered obsolete by registering the service in `composer.json`:
```json
{
    "extra": {
        "spi": {
            "OpenTelemetry\\API\\Instrumentation\\AutoInstrumentation\\Instrumentation": [
                "My\\App\\Custom\\Instrumentation"
            ]
        }
    }
}
```

This plugin provides for extra configuration which allows for packages to self-identify files they no longer need to run at
each runtime to be pruned from the autoloader:
```json
{
    "extra": {
        "opentelemetry": {
            "prune-autoload-files": [
                "_register.php"
            ]
        }
    }
}
```

## Contributing

This repository is a read-only git subtree split.
To contribute, please see the main [OpenTelemetry PHP monorepo](https://github.com/open-telemetry/opentelemetry-php).
