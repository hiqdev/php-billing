Feature: Combination
    Background:
        Given server monthly price is 100 USD per item
          And action is server monthly 1 item

    Scenario Outline: sequential fixed discounts
        Given formula is            discount.since('08.2018').fixed('30%').reason('ONE')
          And formula continues     discount.since('10.2018').fixed('10 USD').reason('TWO')
          And formula continues     discount.since('12.2018').fixed('50%').reason('THREE')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
          And third charge is <third>
          And fourth charge is <fourth>
        Examples:
            | date       | first           | second                        | third                        | fourth                        |
            | 2018-07-31 | monthly 100 USD |                               |                              |                               |
            | 2018-08-01 | monthly 100 USD | discount -30 USD reason ONE   |                              |                               |
            | 2018-09-01 | monthly 100 USD | discount -30 USD reason ONE   |                              |                               |
            | 2018-10-01 | monthly 100 USD | discount -30 USD reason ONE   | discount -10 USD reason TWO  |                               |
            | 2018-11-01 | monthly 100 USD | discount -30 USD reason ONE   | discount -10 USD reason TWO  |                               |
            | 2018-12-01 | monthly 100 USD | discount -30 USD reason ONE   | discount -10 USD reason TWO  | discount -30 USD reason THREE |
            | 2019-01-01 | monthly 100 USD | discount -30 USD reason ONE   | discount -10 USD reason TWO  | discount -30 USD reason THREE |
            | 2028-11-01 | monthly 100 USD | discount -30 USD reason ONE   | discount -10 USD reason TWO  | discount -30 USD reason THREE |

    Scenario Outline: leasing then discount
        Given formula is            installment.since('11.2018').lasts('2 months').reason('ONE')
          And formula continues     discount.since('08.2018').grows('10pp').every('month').max('100%').reason('TWO')
         When action date is <date>
         Then first charge is <first> with <events>
          And second charge is <second>
        Examples:
            | date       | first                      | events                 | second                      |
            | 2018-07-31 | monthly 100 USD            |                        |                             |
            | 2018-08-01 | monthly 100 USD            |                        | discount -10 USD reason TWO |
            | 2018-09-01 | monthly 100 USD            |                        | discount -20 USD reason TWO |
            | 2018-10-01 | monthly 100 USD            |                        | discount -30 USD reason TWO |
            | 2018-11-01 | leasing 100 USD reason ONE | InstallmentWasStarted  |                             |
            | 2018-12-01 | leasing 100 USD reason ONE |                        |                             |
            | 2019-01-11 | leasing   0 USD reason ONE | InstallmentWasFinished |                             |
            | 2028-11-01 |                            |                        |                             |

    Scenario Outline: discounts then leasing
      Given formula is          discount.since('08.2018').grows('10pp').every('month').max('100%').reason('ONE')
      And formula continues     installment.since('11.2018').lasts('2 months').reason('TWO')
      When action date is <date>
      Then first charge is <first> with <events>
      And second charge is <second>
      Examples:
        | date       | first                      | events                 | second                      |
        | 2018-07-31 | monthly 100 USD            |                        |                             |
        | 2018-08-01 | monthly 100 USD            |                        | discount -10 USD reason ONE |
        | 2018-09-01 | monthly 100 USD            |                        | discount -20 USD reason ONE |
        | 2018-10-01 | monthly 100 USD            |                        | discount -30 USD reason ONE |
        | 2018-11-01 | leasing  60 USD reason TWO | InstallmentWasStarted  |                             |
        | 2018-12-01 | leasing  50 USD reason TWO |                        |                             |
        | 2019-01-11 | leasing   0 USD reason TWO | InstallmentWasFinished |                             |
        | 2028-11-01 |                            |                        |                             |

    Scenario Outline: discount with monthly cap
      Given formula is discount.fixed('10 USD').since('04.2022')
        And formula continues cap.monthly('28 days')
        And action is server monthly <qty> item
        And action date is <date>
       Then first charge is <first>
        And second charge is <second>
      Examples:
        | date       | qty      | first                                  | second                    |
        | 2022-01-01 | 1        | monthly 100 USD for 672 hour           | monthly 0 USD for 72 hour |
        | 2022-04-01 | 1        | monthly 90 USD for 672 hour            | monthly 0 USD for 48 hour |

    Scenario Outline: monthly cap, then discount
      # Discount is ignored in this case. Not a desired behavior, but too hard to implement.
      Given formula is cap.monthly('28 days')
        And formula continues discount.fixed('10 USD').since('04.2022')
        And action is server monthly <qty> item
        And action date is <date>
        And sale time is <date>
       Then first charge is <first>
        And second charge is <second>
        And third charge is <third>
      Examples:
        | date       | qty      | first                                  | second                    | third            |
        | 2022-01-01 | 1        | monthly 100 USD for 672 hour           | monthly 0 USD for 72 hour |                  |
        | 2022-04-01 | 1        | monthly 100 USD for 672 hour           | monthly 0 USD for 48 hour |                  |
        | 2022-05-15 | 0.5      | monthly 55.36 USD for 372 hour         |                           |                  |

      Scenario Outline: discount, then monthly cap
      Given formula is discount.fixed('10 USD').since('04.2022')
        And formula continues cap.monthly('28 days')
        And action is server monthly <qty> item
        And action date is <date>
        And sale time is <date>
       Then first charge is <first>
        And second charge is <second>
        And third charge is <third>
      Examples:
        | date       | qty      | first                                  | second                    | third            |
        | 2022-01-01 | 1        | monthly 100 USD for 672 hour           | monthly 0 USD for 72 hour |                  |
        | 2022-04-01 | 1        | monthly 90 USD for 672 hour            | monthly 0 USD for 48 hour |                  |
        | 2022-05-15 | 0.5      | monthly 49.82 USD for 372 hour         |                           |                  |

