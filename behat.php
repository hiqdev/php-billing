<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile(
        (new Profile('default'))
            ->withSuite(
                (new Suite('php-billing'))
                    ->withPaths('%paths.base%/tests/behat')
                    ->withContexts('hiqdev\php\billing\tests\behat\bootstrap\FeatureContext')
            )
    );
