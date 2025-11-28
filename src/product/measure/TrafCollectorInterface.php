<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\measure;

/**
 * - Websa Metering Service - The counters are in a Websa Metering Service
 * Set references to classes in
 * {@see vendor/advancedhosters/hiapi-metering-collector/src/Collectors/CdnTrafMaxCollector.php}
 *
 * - RCP Traf collector
 * {@see vendor/advancedhosters/hiapi-rcp-traf/src/collectors/ServerDuCollector.php}
 *
 * - PeriodicDbQuery - Support Time (stored in our DB)
 * Pass a DBMS function name that handles this price type measurement
 *
 * This interface is not completed this is only a scratch
 */
interface TrafCollectorInterface
{
    public function getClassName(): string;
}
