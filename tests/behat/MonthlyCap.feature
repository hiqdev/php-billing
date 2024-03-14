Feature: Monthly cap
    Background:
        Given server monthly price is 50 USD per item
          And action is server monthly 1 item

  Scenario Outline: Monthly fee for fraction of month
    Given server monthly price is 1488 USD per item
      And formula is cap.monthly('28 days')
      And client rejected service at <unsale_time>
      And action is server monthly 1 item in <sale_time>
     Then first charge is <charge>
      And second charge is <second>
    Examples:
      | description    | sale_time           | unsale_time         | charge                                          | second                                 |
      | Almost 1 month | 2024-02-01 11:50:00 | 2024-02-29 15:15:00 | monthly 1488.00 USD for 672 hour                | monthly 0 USD for 3.4166666666496 hour |
      | A few weeks    | 2024-02-01 11:50:00 | 2024-02-16 15:15:00 | monthly 804.71 USD for 363.41666666666595 hour  |                                        |
      | A few hours    | 2024-02-10 11:50:00 | 2024-02-10 15:15:00 | monthly 7.57 USD for 3.4166666666666687 hour    |                                        |
      | A few minutes  | 2024-02-10 11:50:00 | 2024-02-10 11:55:00 | monthly 0.19 USD for 0.08333333333333567 hour   |                                        |
      | Just a second  | 2024-02-10 11:50:00 | 2024-02-10 11:50:01 | monthly 0.01 USD for 0.0002777777777777789 hour |                                        |


    Scenario Outline: monthly cap for the fixed number of days
        Given formula is cap.monthly('28 days')
          And action is server monthly <qty> item
          And action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | qty      | first                                  | second                        |
            | 2020-09-01 | 1        | monthly 50 USD for 672 hour            | monthly 0 USD for 48 hour     |
            | 2020-10-01 | 1        | monthly 50 USD for 672 hour            | monthly 0 USD for 72 hour     |
            | 2020-11-01 | 1        | monthly 50 USD for 672 hour            | monthly 0 USD for 48 hour     |
            | 2020-12-01 | 0.6451615 | monthly 35.72 USD for 480.000156 hour |                               |
            | 2020-12-01 | 0.9032   | monthly 50 USD for 671.9808 hour       |                               |
            | 2020-12-01 | 0.949    | monthly 50 USD for 672 hour            | monthly 0 USD for 34.056 hour |
            | 2022-02-01 | 1        | monthly 50 USD for 672 hour            |                               |

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
      Given formula is cap.monthly('28 days').since('11.2020')
        And server overuse price is 0.15 USD per GB
        And action is server overuse <qty> gb
        And action date is <date>
        And sale close time is 2023-02-20
       Then first charge is <first>
        And second charge is <second>
      Examples:
        | date       | qty      | first                                  | second                        |
        | 2020-10-01 | 250      | overuse 37.50 USD for 250 GB           |                               |
        | 2020-11-01 | 250      | overuse 37.50 USD for 250 GB           |                               |
        | 2023-02-01 | 250      | overuse 25.45 USD for 250 GB           |                               |
