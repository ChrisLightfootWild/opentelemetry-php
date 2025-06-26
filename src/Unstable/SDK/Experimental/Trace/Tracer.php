<?php

declare(strict_types=1);

namespace OpenTelemetry\SDK\Experimental\Trace;

class Tracer extends \OpenTelemetry\SDK\Trace\Tracer
{
    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }
}
