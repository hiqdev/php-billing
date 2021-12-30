Feature: Monthly cap for overuses
    Background:
        Given server overuse price is 0.1 USD per gb
          And action is server overuse 42 gb
          And sale is since "2020-08-15" till "2021-02-22"

    Scenario Outline: monthly cap for the fixed number of days
        Given formula is cap.monthly('28 days')
          And action is server overuse <qty> gb
          And action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | qty      | first                                  | second                        |
            | 2020-08-15 | 42       | overuse 2.30 USD for 42 gb             |                               |
            | 2020-09-01 | 42       | overuse 4.20 USD for 42 gb             | overuse 0 USD for 48 hour     |
            | 2020-10-01 | 42       | overuse 4.20 USD for 42 gb             | overuse 0 USD for 72 hour     |
            | 2021-02-01 | 42       | overuse 3.15 USD for 42 gb             |                               |
