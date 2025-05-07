<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\invoice;

use hiqdev\php\billing\product\price\PriceTypeDefinition;
use hiqdev\php\billing\product\trait\HasLock;
use hiqdev\php\billing\product\trait\HasLockInterface;
use IteratorAggregate;
use Traversable;

/**
 * @template T of PriceTypeDefinition
 * @implements IteratorAggregate<int, RepresentationInterface>
 */
class RepresentationCollection implements IteratorAggregate, HasLockInterface
{
    use HasLock;

    private array $representations = [];

    private RepresentationUniquenessGuard $uniquenessGuard;

    public function __construct(private readonly PriceTypeDefinition $priceTypeDefinition)
    {
        $this->uniquenessGuard = new RepresentationUniquenessGuard();
    }

    /**
     * @return Traversable<int, RepresentationInterface>
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->representations);
    }

    public function attach(RepresentationInterface $representation): self
    {
        $this->ensureNotLocked();

        $representation->setType($this->priceTypeDefinition->type());

        $this->uniquenessGuard->ensureUnique($representation);

        $this->representations[] = $representation;

        return $this;
    }

    /**
     * @psalm-return T
     */
    public function end(): PriceTypeDefinition
    {
        return $this->priceTypeDefinition;
    }

    public function filterByType(string $className): array
    {
        return array_filter($this->representations, fn($r) => $r instanceof $className);
    }
}
