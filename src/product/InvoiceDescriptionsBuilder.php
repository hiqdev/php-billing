<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class InvoiceDescriptionsBuilder
{
    public function __construct(
        private readonly BillingRegistry $registry
    ) {
    }

    public function build(): array
    {
        $descriptions = [];
        foreach ($this->registry->priceTypes() as $priceType) {
            $descriptions[] = $priceType->documentRepresentation();
        }

        return $descriptions;
    }
}
