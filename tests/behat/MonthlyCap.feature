Feature: Monthly cap
    Background:
        Given server monthly price is 50 USD per item
          And action is server monthly 1 item

  Scenario Outline: Monthly fee for fraction of month
    Given server monthly price is 1488 USD per item
      And formula is cap.monthly('28 days')
      And client rejected service at <unsale_time>
      And action is server monthly 1 item in <sale_time>
      And sale time is <sale_time>
     Then first charge is <charge>
      And second charge is <second>
    Examples:
      | description    | sale_time           | unsale_time         | charge                                          | second                                 |
      | Almost 1 month | 2024-02-01 11:50:00 | 2024-02-29 18:15:00 | monthly 1488.00 USD for 672 hour                | monthly 0 USD for 6.4166666666666 hour |
      | A few weeks    | 2024-02-01 11:50:00 | 2024-02-16 15:15:00 | monthly 804.71 USD for 363.41666666666595 hour  |                                        |
      | A few hours    | 2024-02-10 11:50:00 | 2024-02-10 15:15:00 | monthly 7.56 USD for 3.4166666666666687 hour    |                                        |
      | A few minutes  | 2024-02-10 11:50:00 | 2024-02-10 11:55:00 | monthly 0.19 USD for 0.08333333333333567 hour   |                                        |
      | Just a second  | 2024-02-10 11:50:00 | 2024-02-10 11:50:01 | monthly 0.01 USD for 0.0002777777777777789 hour |                                        |


    Scenario Outline: monthly cap for the fixed number of days
        Given formula is cap.monthly('28 days')
          And action is server monthly <qty> item
          And action date is <date>
          And sale time is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date                  | qty         | first                                  | second                        |
            | 2020-09-01            | 1           | monthly 50 USD for 672 hour            | monthly 0 USD for 48 hour     |
            | 2020-10-01            | 1           | monthly 50 USD for 672 hour            | monthly 0 USD for 72 hour     |
            | 2020-11-01            | 1           | monthly 50 USD for 672 hour            | monthly 0 USD for 48 hour     |
            | 2020-12-10            | 0.6451615   | monthly 35.72 USD for 528 hour         |                               |
            | 2020-12-04            | 0.9032258   | monthly 50.00 USD for 672 hour         |                               |
            | 2020-12-03 14:00:00   | 0.9166665   | monthly 50.00 USD for 672 hour         | monthly 0 USD for 10 hour     |
            | 2022-02-01            | 1           | monthly 50 USD for 672 hour            |                               |

    Scenario Outline: monthly cap for the fixed number of days when cap is longer then the shortest month
        Given formula is cap.monthly('30 days')
          And action is server monthly <qty> item
          And action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | qty      | first                                  | second                        |
            | 2020-09-01 | 1        | monthly 50 USD for 720 hour            |                               |
            | 2020-10-01 | 1        | monthly 50 USD for 720 hour            | monthly 0 USD for 24 hour     |
            | 2022-02-01 | 1        | monthly 46.67 USD for 672 hour         |                               |

    Scenario Outline: monthly cap for the fixed number of days since month
        Given formula is cap.monthly('28 days').since('11.2020')
          And action is server monthly <qty> item
          And action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
          | date       | qty      | first                                  | second                        |
          | 2020-10-01 | 1        | monthly 50 USD for 1 item              |                               |
          | 2020-11-01 | 1        | monthly 50 USD for 672 hour            | monthly 0 USD for 48 hour     |
          | 2022-02-01 | 1        | monthly 50 USD for 672 hour            |                               |

    Scenario Outline: monthly cap on overuses
      Given formula is cap.monthly('28 days').since('11.2020').forNonProportionalizedQuantity()
        And server overuse price is 0.15 USD per GB
        And action date is <date>
        And sale close time is 2023-02-20
        And action is server overuse <qty> gb in <date>
       Then first charge is <first>
        And second charge is <second>
      Examples:
        | date       | qty            | first                                  | second                        |
        | 2020-10-01 | 250            | overuse 37.50 USD for 250 GB           |                               |
        | 2020-11-01 | 250            | overuse 37.50 USD for 250 GB           |                               |
        | 2023-02-01 | 250            | overuse 25.45 USD for 250 GB           |                               |

    Scenario Outline: monthly cap on item overuses
      # Use case: A service with pre-defined quantity of items, where overuse is charged per item
      Given formula is cap.monthly('28 days')
        And server overuse price is 12 USD per item
        And action is server overuse <qty> items
        And action date is <date>
        And sale time is <date>
       Then first charge is <first>
        And second charge is <second>
      Examples:
        | date                | qty                | first                                      | second                                 |
        | 2023-09-01          | 2                  | overuse 24 USD for 672 hour                | overuse 0 USD for 48 hour              |
        | 2023-09-02 09:41:16 | 1.9064228395061729 | overuse 24 USD for 672 hour                | overuse 0 USD for 14.312222222222 hour |
        | 2023-09-03 12:41:16 | 1.8314228395       | overuse 23.55 USD for 659.31222222222 hour |                                        |
        | 2023-09-01          | 1.5                | overuse 18 USD for 672 hour                |                                        |

    # Use case: Volume DU should be billed for byte*months under monthly cap.
    # The use.amount is stored in bytes;
    # The action.amount is proportionalized and stored in byte*months;
    # The bill and charge qty should be in hours under monthly cap.
    # When need to extract the effective bytes qty, use action.amount/fraction_of_month
    # TODO: It would be nice to introduce a new unit tree for byte*time, then we will be able to store charge.qty in byte*hours,
    Scenario Outline: monthly cap on volume overuses
      Given formula is cap.monthly('28 days')
        And server overuse price is 0.02 USD per GB
        And action date is <date>
        And sale time is <date>
        And client rejected service at <unsale_time>
        And action is server overuse <action_amount> bytes in <date>
       Then first charge is <first>
        And second charge is <second>
      Examples:
        | date                | unsale_time         | action_amount | first                                      | second                    |
#        | 2024-10-01          |                     | 5500000000000 | overuse 110 USD for 672 hour               | overuse 0 USD for 72 hour |
        | 2024-10-16 10:55:15 | 2024-10-23 06:58:09 | 1212722894265 | overuse 26.85 USD for 164.04833333333 hour |                           |
#        | 2024-10-23 06:58:09 |                     | 1685732526881 | overuse 37.32 USD for 209.03083333333 hour |                           |

