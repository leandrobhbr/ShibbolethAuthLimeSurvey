<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withRootFiles()
    /*
    ->withFileExtensions([
      'php'
    ])
     */
    ->withSkipPath(
      __DIR__ . '/vendor'
    )
    // uncomment to reach your current PHP version
    ->withPhpSets(php81: true)
    ->withPreparedSets(deadCode: true, codeQuality: true)
    ->withTypeCoverageLevel(0);
