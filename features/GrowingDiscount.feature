Feature: Growing discount

    Scenario Outline: relative discount growing 1% every month from 5% up to 10%
        Given formula discount.since('08.2018').grows('1%').everyMonth().from('5%').max('10%')
        When date is <date>
        Then charges are <original> plus <discounts>
        Examples:
            | date       | original | discounts |
            | 2018-07-31 | original |           |

    Scenario Outline: relative discount growing 1% every month from 5% since 08.2018 till 08.2019
        Given formula discount.since('08.2018').till('08.2019').grows('1%').everyMonth().from('5%')
        When date is <date>
        Then charges are <original> plus <discounts>
        Examples:
            | date       | original | discounts |
            | 2018-07-31 | original |           |
