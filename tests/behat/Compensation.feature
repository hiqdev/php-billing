Feature: Compensation
    Background:
        Given server overuse price is 1 USD per tb includes 10

    Scenario Outline: credit note
        Given formula is discount.fixed('5 tb').as('compensation').since('02.2018').till('11.2018').reason('bonus')
          And action is server overuse <overuse>
         When action date is <date>
         Then first charge is <first>
         Then second charge is <second>
        Examples:
            | date       | overuse   | first            | second                             |
            | 2018-01-01 | 19 tb     | overuse 9 USD    |                                    |
            | 2018-02-01 | 2 tb      |                  |                                    |
            | 2018-03-01 | 0 tb      |                  |                                    |
            | 2018-04-01 | 10.1 tb   | overuse 0.1 USD  | compensation -0.1 USD reason bonus |
            | 2018-05-01 | 2000 byte |                  |                                    |
            | 2018-06-01 | 180 tb    | overuse 170 USD  | compensation -5 USD reason bonus   |
            | 2018-07-01 | 19 tb     | overuse 9 USD    | compensation -5 USD reason bonus   |
            | 2018-08-01 | 18 tb     | overuse 8 USD    | compensation -5 USD reason bonus   |
            | 2018-09-01 | 15 tb     | overuse 5 USD    | compensation -5 USD reason bonus   |
            | 2018-10-01 | 14 tb     | overuse 4 USD    | compensation -4 USD reason bonus   |
            | 2018-11-01 | 19 tb     | overuse 9 USD    |                                    |
