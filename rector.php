<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\BooleanAnd\SimplifyEmptyArrayCheckRector;
use Rector\CodeQuality\Rector\Concat\JoinStringConcatRector;
use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\FuncCall\SimplifyRegexPatternRector;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\CodeQuality\Rector\Ternary\SwitchNegatedTernaryRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassConst\RemoveUnusedPrivateClassConstantRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\ConstFetch\RemovePhpVersionIdCheckRector;
use Rector\EarlyReturn\Rector\Foreach_\ChangeNestedForeachIfsToEarlyContinueRector;
use Rector\EarlyReturn\Rector\If_\ChangeIfElseValueAssignToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\ChangeNestedIfsToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;

/**
 * How to run:
 *
 * ```bash
 * composer require rector/rector --dev
 * ./vendor/bin/rector process
 * ```
 */
return RectorConfig::configure()
    ->withAutoloadPaths([
        file_exists(__DIR__ . '/../../autoload.php') ? __DIR__ . '/../../autoload.php' : null,
        file_exists(__DIR__ . '/vendor/autoload.php') ? __DIR__ . '/vendor/autoload.php' : null,
    ])
    ->withCache(__DIR__ . '/../../../runtime/cache/rector', FileCacheStorage::class)
    ->withPreparedSets(deadCode: true, codeQuality: true)
    ->withPhpSets(php83: true, php84: true)
    ->withPhpVersion(\Rector\ValueObject\PhpVersion::PHP_84)
    ->withoutParallel()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withRules([
        RemoveAlwaysElseRector::class,
        ChangeNestedIfsToEarlyReturnRector::class,
        ChangeIfElseValueAssignToEarlyReturnRector::class,
        ChangeNestedForeachIfsToEarlyContinueRector::class,
    ])
    ->withSkip([
        // Too many places where json_decode was used intentionally on wrong data =\
        JsonThrowOnErrorRector::class,

        // Frequently used for debug. Maybe, such places should be annotated with @noRector?
        SimplifyUselessVariableRector::class,

        // Legacy modules are full of `_doSomeShit()` methods that are likely called as `$this->_do{$operation}`
        RemoveUnusedPrivateMethodRector::class,

        // Does not make sense for us
        SimplifyRegexPatternRector::class,

        // Requires whole project autoloaded to work well
        RemoveUnusedPrivateClassConstantRector::class,

        // don't refactor with `empty` check function
        DisallowedEmptyRuleFixerRector::class,
        SimplifyEmptyArrayCheckRector::class,
        SimplifyEmptyCheckOnEmptyArrayRector::class,

        // do not refactor `query` attribute like " . '$select' . " to 'SELECT $select ...'
        JoinStringConcatRector::class,

        // do not refactor php version id checkers
        RemovePhpVersionIdCheckRector::class,
        RemoveExtraParametersRector::class,

        SwitchNegatedTernaryRector::class,
    ]);
