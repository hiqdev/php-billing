Feature: Growing discount
    Background:
        Given server monthly price is 50 USD per unit
          And action is server monthly 2 units

    Scenario: discount can not be unlimited
        Given formula is discount.since('01.2018').grows('1%').every('month').min('5%')
         Then error is growing discount must be limited
         When calculating charges

    Scenario: discount minimum must match step
        Given formula is discount.since('08.2018').grows('1%').every('month').min('10 USD')
         Then error is minimum must be relative
         When calculating charges

    Scenario Outline: relative discount growing 30% every month
        Given formula is discount.since('08.2000').grows('30%').every('year').max('100%')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second              |
            | 2000-07-31 | monthly 100 USD |                     |
            | 2000-08-01 | monthly 100 USD | discount -30.00 USD |
            | 2001-08-01 | monthly 100 USD | discount -51.00 USD |
            | 2002-08-01 | monthly 100 USD | discount -65.70 USD |
            | 2003-08-01 | monthly 100 USD | discount -75.99 USD |
            | 2004-08-01 | monthly 100 USD | discount -83.19 USD |
            | 2005-08-01 | monthly 100 USD | discount -88.24 USD |
            | 2011-08-01 | monthly 100 USD | discount -98.62 USD |
            | 2024-08-01 | monthly 100 USD | discount -99.99 USD |

    Scenario Outline: relative discount growing 20% every month from given min to max
        Given formula is discount.since('08.2000').grows('20%').every('1 year').min('10%').max('75%')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second              |
            | 2000-07-31 | monthly 100 USD |                     |
            | 2000-08-01 | monthly 100 USD | discount -10.00 USD |
            | 2001-08-01 | monthly 100 USD | discount -28.00 USD |
            | 2002-08-01 | monthly 100 USD | discount -42.40 USD |
            | 2003-08-01 | monthly 100 USD | discount -53.92 USD |
            | 2004-08-01 | monthly 100 USD | discount -63.14 USD |
            | 2005-08-01 | monthly 100 USD | discount -70.51 USD |
            | 2010-08-01 | monthly 100 USD | discount -75.00 USD |
            | 2038-08-01 | monthly 100 USD | discount -75.00 USD |

    Scenario Outline: absolute discount growing every 2 month from min to max
        Given formula is discount.since('08.2018').grows('20 USD').every('2 months').min('15 USD').max('80 USD')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second           |
            | 2018-07-31 | monthly 100 USD |                  |
            | 2018-08-01 | monthly 100 USD | discount -30 USD |
            | 2018-09-01 | monthly 100 USD | discount -30 USD |
            | 2018-10-01 | monthly 100 USD | discount -70 USD |
            | 2018-11-01 | monthly 100 USD | discount -70 USD |
            | 2018-12-01 | monthly 100 USD | discount -80 USD |
            | 2019-01-01 | monthly 100 USD | discount -80 USD |
            | 2028-11-01 | monthly 100 USD | discount -80 USD |

    Scenario Outline: discount maximum may not match step: relative step but absolute max
        Given formula is discount.since('08.2018').grows('20pp').every('month').min('30%').max('77 USD')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second           |
            | 2018-07-31 | monthly 100 USD |                  |
            | 2018-08-01 | monthly 100 USD | discount -30 USD |
            | 2018-09-01 | monthly 100 USD | discount -50 USD |
            | 2018-10-01 | monthly 100 USD | discount -70 USD |
            | 2018-11-01 | monthly 100 USD | discount -77 USD |
            | 2018-12-01 | monthly 100 USD | discount -77 USD |
            | 2028-11-01 | monthly 100 USD | discount -77 USD |

    Scenario Outline: relative discount with since but till and without min
        Given formula is discount.since('08.2018').till('12.2018').grows('10pp').every('month')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second           |
            | 2018-07-31 | monthly 100 USD |                  |
            | 2018-08-01 | monthly 100 USD | discount -10 USD |
            | 2018-09-01 | monthly 100 USD | discount -20 USD |
            | 2018-10-01 | monthly 100 USD | discount -30 USD |
            | 2018-11-01 | monthly 100 USD | discount -40 USD |
            | 2018-12-01 | monthly 100 USD |                  |
            | 2028-11-01 | monthly 100 USD |                  |

