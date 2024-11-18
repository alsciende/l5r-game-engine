<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\CyclomaticComplexitySniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    ->withPreparedSets(
        psr12: true,
        common: true,
        strict: true,
        cleanCode: true,
    )
    ->withPhpCsFixerSets(
        symfony: true
    )
    ->withConfiguredRule(CyclomaticComplexitySniff::class, [])
;
