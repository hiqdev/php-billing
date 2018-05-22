Feature: Growing discount
    Background:
        Given server monthly price is 50 USD per unit
          And action is server monthly 2 units

    Scenario: discount can not be unlimited
        Given formula is discount.since('08.2018').grows('1%').every('month').min('5%')
          And action date is 2018-08-01
         Then error is growing discount must be limited
         When calculating charges

    Scenario: discount minimum must match step
        Given formula is discount.since('08.2018').grows('1%').every('month').min('10 USD')
          And action date is 2018-08-01
         Then error is minimum must be relative
         When calculating charges

    Scenario Outline: relative discount growing every month from min to max
        Given formula is discount.since('08.2018').grows('2%').every('1 month').min('5%').max('10%')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second          |
            | 2018-07-31 | monthly 100 USD |                 |
            | 2018-08-01 | monthly 100 USD | discount  5 USD |
            | 2018-09-01 | monthly 100 USD | discount  7 USD |
            | 2018-10-01 | monthly 100 USD | discount  9 USD |
            | 2018-11-01 | monthly 100 USD | discount 10 USD |
            | 2018-12-01 | monthly 100 USD | discount 10 USD |
            | 2019-01-01 | monthly 100 USD | discount 10 USD |
            | 2028-11-01 | monthly 100 USD | discount 10 USD |

    Scenario Outline: absolute discount growing every 2 month from min to max
        Given formula is discount.since('08.2018').grows('20 USD').every('2 months').min('50 USD').max('80 USD')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second          |
            | 2018-07-31 | monthly 100 USD |                 |
            | 2018-08-01 | monthly 100 USD | discount 50 USD |
            | 2018-09-01 | monthly 100 USD | discount 50 USD |
            | 2018-10-01 | monthly 100 USD | discount 70 USD |
            | 2018-11-01 | monthly 100 USD | discount 70 USD |
            | 2018-12-01 | monthly 100 USD | discount 80 USD |
            | 2019-01-01 | monthly 100 USD | discount 80 USD |
            | 2028-11-01 | monthly 100 USD | discount 80 USD |

    Scenario Outline: discount maximum may not match step: relative step but absolute max
        Given formula is discount.since('08.2018').grows('20%').every('month').min('30%').max('77 USD')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second          |
            | 2018-07-31 | monthly 100 USD |                 |
            | 2018-08-01 | monthly 100 USD | discount 30 USD |
            | 2018-09-01 | monthly 100 USD | discount 50 USD |
            | 2018-10-01 | monthly 100 USD | discount 70 USD |
            | 2018-11-01 | monthly 100 USD | discount 77 USD |
            | 2018-12-01 | monthly 100 USD | discount 77 USD |
            | 2028-11-01 | monthly 100 USD | discount 77 USD |

    Scenario Outline: relative discount with since but till and without min
        Given formula is discount.since('08.2018').till('11.2018').grows('10%').every('month')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second          |
            | 2018-07-31 | monthly 100 USD |                 |
            | 2018-08-01 | monthly 100 USD | discount 10 USD |
            | 2018-09-01 | monthly 100 USD | discount 20 USD |
            | 2018-10-01 | monthly 100 USD | discount 30 USD |
            | 2018-11-01 | monthly 100 USD |                 |
            | 2018-12-01 | monthly 100 USD |                 |
            | 2028-11-01 | monthly 100 USD |                 |
