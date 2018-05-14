# Fixed discount

> discount.since('08.2018').fixed('100 USD')

2018-07-05: original
2018-08-01: original + discount('100 USD')
2018-08-22: original + discount('100 USD')
2018-09-01: original + discount('100 USD')
2028-11-11: original + discount('100 USD')

> discount.since('08.2018').fixed('2%')

2018-07-05: original
2018-08-01: original + discount('2 USD')
2018-08-22: original + discount('2 USD')
2018-09-01: original + discount('2 USD')
2028-11-11: original + discount('2 USD')


# Growing discount

> discount.since('08.2018').grows('1%').everyMonth().from('5%').max('10%')

> discount.since('08.2018').till('08.2019').grows('1%').everyMonth().from('5%').max('10%')
