# Fixed discount

discount.since('08.2018').fixed('100 USD')
------------------------------------------

| Date       | Charges                        |
|------------|--------------------------------|
| 2018-07-01 | original                       |
| 2018-07-31 | original                       |
| 2018-08-01 | original + discount('100 USD') |
| 2018-08-22 | original + discount('100 USD') |
| 2018-09-01 | original + discount('100 USD') |
| 2028-11-11 | original + discount('100 USD') |

discount.since('08.2018').fixed('2%')
-------------------------------------

| Date       | Charges                      |
|------------|------------------------------|
| 2018-07-05 | original                     |
| 2018-08-01 | original + discount('2 USD') |
| 2018-08-22 | original + discount('2 USD') |
| 2018-09-01 | original + discount('2 USD') |
| 2028-11-11 | original + discount('2 USD') |

# Growing discount

discount.since('08.2018').grows('1%').everyMonth().from('5%').max('3%')
------------------------------------------------------------------------

discount.since('08.2018').till('10.2019').grows('1%').everyMonth().from('5%')
-----------------------------------------------------------------------------

# Multiline discounts

discount.since('01.2018').fixed('5%')<br>
discount.since('04.2018').fixed('15 USD')
-----------------------------------------
