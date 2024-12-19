<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class InvoiceDescriptionsBuilder
{
    private BillingRegistry $registry;

    public function __construct(BillingRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function build(): array
    {
        $descriptions = [];
        foreach ($this->registry->priceTypes() as $priceType) {
            $descriptions[] = $priceType->representInvoice();
        }

        return $descriptions;
    }
}
