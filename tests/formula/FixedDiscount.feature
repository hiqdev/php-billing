Feature: Fixed discount

    Scenario: absolute fixed discount with since date
        Given formula discount.since('08.2018').fixed('100 USD')
        When date is <date>
        Then charges are <charges>
        Examples:
            | date       | charges                        |
            | 2018-07-01 | original                       |
            | 2018-07-31 | original                       |
            | 2018-08-01 | original + discount('100 USD') |
            | 2018-08-22 | original + discount('100 USD') |
            | 2018-09-01 | original + discount('100 USD') |
            | 2028-11-11 | original + discount('100 USD') |

    Scenario: relative fixed discount with since date
        Given formula discount.since('08.2018').fixed('2%')
        When date is <date>
        Then charges are <charges>
        Examples:
            | date       | charges                      |
            | 2018-07-05 | original                     |
            | 2018-08-01 | original + discount('2 USD') |
            | 2018-08-22 | original + discount('2 USD') |
            | 2018-09-01 | original + discount('2 USD') |
            | 2028-11-11 | original + discount('2 USD') |
