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
         Then first charge is <first>
          And second charge is 
        Examples:
            | date       | first                       |
            | 2018-07-01 |                             |
            | 2018-08-01 | monthly 100 USD reason TEST |
            | 2018-09-01 | monthly 100 USD reason TEST |
            | 2018-10-01 | monthly 100 USD reason TEST |
            | 2018-11-01 |                             |
            | 2028-01-01 |                             |
