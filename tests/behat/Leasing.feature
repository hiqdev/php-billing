Feature: Leasing
    Background:
        Given server monthly price is 100 USD per item
          And action is server monthly 1 item

    Scenario: leasing can't have `till`
        Given formula is leasing.since('08.2018').till('10.2018')
         Then error is till can not be defined for leasing
         When calculating charges

    Scenario Outline: simple leasing with reason
        Given formula is leasing.since('08.2018').lasts('3 months').reason('TEST')
         When action date is <date>
         Then first charge is <first> with <events>
        Examples:
            | date       | first                       | events                     |
            | 2018-07-01 |                             |                            |
            | 2018-08-01 | leasing 100 USD reason TEST | LeasingWasStarted          |
            | 2018-09-01 | leasing 100 USD reason TEST |                            |
            | 2018-10-01 | leasing 100 USD reason TEST |                            |
            | 2018-11-01 | leasing 0 USD reason TEST   | LeasingWasFinished         |
            | 2028-01-01 |                             |                            |

    Scenario Outline: simple installment with reason
      Given formula is leasing.since('01.2024').lasts('3 months').reason('TEST')
      When action date is <date>
      Then first charge is <first> with <events>
      Examples:
        | date       | first                            | events                     |
        | 2023-12-01 |                                  |                            |
        | 2024-01-01 | installment 100 USD reason TEST  | InstallmentWasStarted          |
        | 2024-02-01 | installment 100 USD reason TEST  |                            |
        | 2024-03-01 | installment 100 USD reason TEST  |                            |
        | 2024-04-01 | installment 0 USD reason TEST    | LeasingWasFinished         |
        | 2028-01-01 |                                  |                            |

    Scenario Outline: leasing will not work when price is zero
          Given server monthly price is 0 USD per item
            And formula is leasing.since('08.2018').lasts('3 months').reason('TEST')
           When action date is <date>
           Then first charge is <first> with <events>
        Examples:
          | date       | first                     | events             |
          | 2018-07-01 |                           |                    |
          | 2018-08-01 | leasing 0 USD reason TEST | LeasingWasStarted  |
          | 2018-09-01 | leasing 0 USD reason TEST |                    |
          | 2018-10-01 | leasing 0 USD reason TEST |                    |
          | 2018-11-01 | leasing 0 USD reason TEST | LeasingWasFinished |
          | 2028-01-01 |                           |                    |
