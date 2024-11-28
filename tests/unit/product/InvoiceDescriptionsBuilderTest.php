<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product;

use PHPUnit\Framework\TestCase;

class InvoiceDescriptionsBuilderTest extends TestCase
{
    public function testInvoiceDescriptionsBuilderWithRealClasses()
    {
        // Create TariffType with real prices and behaviors
        $serverTariffType = (new TariffType('server'))
            ->ofProduct(ServerProduct::class)
            ->setPricesSuggester(\hiapi\legacy\lib\billing\price\suggester\device\ServerPricesSuggester::class)
            ->withPrices()
                ->monthly('support_time')
                    ->unit('hour')
                    ->description('Monthly fee for support time')
                    ->quantityFormatter(MonthlyQuantityFormatter::class)
                    ->invoiceRepresentation(function () {
                        return 'Invoice for support_time (monthly): $100';
                    })
                    ->end()
                ->overuse('support_time')
                    ->unit('hour')
                    ->description('Support time overuse')
                    ->quantityFormatter(HourBasedOveruseQuantityFormatter::class)
                    ->invoiceRepresentation(function () {
                        return 'Invoice for support_time (overuse): $50';
                    })
                ->end()
            ->end()  // Returns control to TariffType
            ->withBehaviors()
            ->attach(new OncePerMonthPlanChangeBehavior())
            ->end();

        // Create BillingRegistry and add the TariffType
        $billingRegistry = new BillingRegistry();
        $billingRegistry->addTariffType($serverTariffType);
        $billingRegistry->lock();

        // Build invoice descriptions
        $builder = new InvoiceDescriptionsBuilder($billingRegistry);
        $invoiceDescriptions = $builder->build();

        // Verify the results
        $this->assertIsArray($invoiceDescriptions, 'build() should return an array of invoice descriptions.');
        $this->assertCount(2, $invoiceDescriptions, 'There should be 2 invoice descriptions generated.');

        $this->assertSame(
            'Invoice for support_time (monthly): $100',
            $invoiceDescriptions[0],
            'The first description should match the expected monthly invoice description.'
        );

        $this->assertSame(
            'Invoice for support_time (overuse): $50',
            $invoiceDescriptions[1],
            'The second description should match the expected overuse invoice description.'
        );
    }
}
