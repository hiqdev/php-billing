<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\DataMapper\Hydrator\GeneratedHydrator;

class SumsHydrator extends GeneratedHydrator
{
    public function hydrate(array $data, $object): object
    {
        if (!isset($data['sums'])) {
            throw new PriceInvalidArgumentException('Missing required key: sums');
        }
        return new Sums($data['sums']);
    }

    /**
     * {@inheritdoc}
     * @param Sums $object
     */
    public function extract($object): array
    {
        return [
            'values'  => $object->values(),
        ];
    }
}
