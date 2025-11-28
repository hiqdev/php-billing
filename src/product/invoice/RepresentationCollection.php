<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\invoice;

use hiqdev\php\billing\product\price\PriceTypeDefinition;
use hiqdev\php\billing\product\trait\HasLock;
use hiqdev\php\billing\product\trait\HasLockInterface;
use IteratorAggregate;
use Traversable;

/**
 * @template T
 * @implements IteratorAggregate<int, RepresentationInterface>
 * @psalm-consistent-templates
 */
class RepresentationCollection implements IteratorAggregate, HasLockInterface
{
    use HasLock;

    private array $representations = [];

    private RepresentationUniquenessGuard $uniquenessGuard;

    /**
     * @psalm-var T
     */
    private readonly PriceTypeDefinition $priceTypeDefinition;

    /**
     * @psalm-param T $priceTypeDefinition
     */
    public function __construct(
        PriceTypeDefinition $priceTypeDefinition,
    ) {
        $this->priceTypeDefinition = $priceTypeDefinition;
        $this->uniquenessGuard = new RepresentationUniquenessGuard();
    }

    /**
     * @return Traversable<int, RepresentationInterface>
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->representations);
    }

    public function attach(RepresentationInterface $representation): static
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
    public function end()
    {
        return $this->priceTypeDefinition;
    }

    public function filterByType(string $className): array
    {
        return array_filter($this->representations, fn($r) => $r instanceof $className);
    }
}
