Feature: Monthly cap
    Background:
        Given server monthly price is 50 USD per item
          And action is server monthly 1 item

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
