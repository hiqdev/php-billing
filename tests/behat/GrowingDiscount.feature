Feature: Growing discount
    Background:
        Given server monthly price is 50 USD per unit
          And action is server monthly 2 units

    Scenario: growing discount without limit
        Given formula is discount.since('08.2018').grows('1%').everyMonth().min('5%')
          And action date is 2018-08-01
        Then error is growing discount must be limited
        When calculating charges

    Scenario Outline: relative discount growing 1% every month from 5% up to 10%
        Given formula is discount.since('08.2018').grows('1%').everyMonth().min('5%').max('10%')
        When action date is <date>
        Then first charge is <first>
         And second charge is <second>
        Examples:
            | date       | first           | second    |
            | 2018-07-31 | monthly 100 USD |           |

    Scenario Outline: relative discount growing 1% every month from 5% since 08.2018 till 08.2019
        Given formula is discount.since('08.2018').till('08.2019').grows('1%').everyMonth().min('5%')
        When action date is <date>
        Then first charge is <first>
         And second charge is <second>
        Examples:
            | date       | first           | second    |
            | 2018-07-31 | monthly 100 USD |           |
