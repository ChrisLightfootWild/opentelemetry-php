<?php

declare(strict_types=1);

namespace OpenTelemetry\API\Experimental\Trace;

/**
 * @experimental
 */
interface TracerInterface extends \OpenTelemetry\API\Trace\TracerInterface
{
    /**
     * Determine if the tracer is enabled. Instrumentation authors SHOULD call this method prior to
     * creating a new span.
     */
    public function isEnabled(): bool;
}
