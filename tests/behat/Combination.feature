Feature: Combination
    Background:
        Given server monthly price is 100 USD per item
          And action is server monthly 1 item

    Scenario Outline: sequential fixed discounts
        Given formula is            discount.since('08.2018').fixed('30%').reason('ONE')
          And formula continues     discount.since('10.2018').fixed('11 USD').reason('TWO')
          And formula continues     discount.since('12.2018').fixed('55%').reason('THREE')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second                       |
            | 2018-07-31 | monthly 100 USD |                              |
            | 2018-08-01 | monthly 100 USD | discount 30 USD reason ONE   |
            | 2018-09-01 | monthly 100 USD | discount 30 USD reason ONE   |
            | 2018-10-01 | monthly 100 USD | discount 11 USD reason TWO   |
            | 2018-11-01 | monthly 100 USD | discount 11 USD reason TWO   |
            | 2018-12-01 | monthly 100 USD | discount 55 USD reason THREE |
            | 2019-01-01 | monthly 100 USD | discount 55 USD reason THREE |
            | 2028-11-01 | monthly 100 USD | discount 55 USD reason THREE |

    Scenario Outline: discounts then leasing
        Given formula is            discount.since('08.2018').grows('10%').every('month').max('100%').reason('ONE')
          And formula continues     leasing.since('11.2018').lasts('2 months').reason('TWO')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first                      | second                      |
            | 2018-07-31 | monthly 100 USD            |                             |
            | 2018-08-01 | monthly 100 USD            | discount  10 USD reason ONE |
            | 2018-09-01 | monthly 100 USD            | discount  20 USD reason ONE |
            | 2018-10-01 | monthly 100 USD            | discount  30 USD reason ONE |
            | 2018-11-01 | monthly 100 USD reason TWO |                             |
            | 2018-12-01 | monthly 100 USD reason TWO |                             |
            | 2019-01-01 |                            |                             |
            | 2028-11-01 |                            |                             |
