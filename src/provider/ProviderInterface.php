<?php

namespace hiqdev\php\billing\provider;

interface ProviderInterface
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * Globally unique ID.
     *
     * @return int|string
     */
    public function getUniqueId();
}
