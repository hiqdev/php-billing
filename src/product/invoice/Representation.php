<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\invoice;

use hiqdev\php\billing\type\TypeInterface;

/**
 * This class is made abstract intentionally.
 * Because you can attach multiple representations, and thus you should distinguish them somehow.
 * So, please implement you own representation.
 */
abstract class Representation implements RepresentationInterface
{
    private TypeInterface $type;

    public function __construct(private readonly string $sql)
    {
        if (trim($this->sql) === '') {
            throw new InvalidRepresentationException('Representation SQL cannot be empty.');
        }
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function setType(TypeInterface $type): RepresentationInterface
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): TypeInterface
    {
        return $this->type;
    }
}
