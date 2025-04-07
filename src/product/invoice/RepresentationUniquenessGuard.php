<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\invoice;

class RepresentationUniquenessGuard
{
    private array $keys = [];

    public function ensureUnique(RepresentationInterface $representation): void
    {
        $key = $this->generateKey($representation);

        if (isset($this->keys[$key])) {
            throw new DuplicateRepresentationException("Duplicate '$key' representation");
        }

        $this->keys[$key] = true;
    }

    private function generateKey(RepresentationInterface $representation): string
    {
        $reflect = new \ReflectionClass($representation);
        return $reflect->getShortName() . ':' . $representation->getType()->getName();
    }
}
