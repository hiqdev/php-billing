Feature: Fixed discount
    Background:
        Given server monthly price is 50 USD per unit
          And action is server monthly 2 units

    Scenario Outline: absolute fixed discount without limits with reason
        Given formula is discount.fixed('50 USD').reason('TEST COMMENT')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second                               |
            | 2018-07-01 | monthly 100 USD | discount -50 USD reason TEST COMMENT |
            | 2018-08-01 | monthly 100 USD | discount -50 USD reason TEST COMMENT |
            | 2018-09-01 | monthly 100 USD | discount -50 USD reason TEST COMMENT |
            | 2028-11-11 | monthly 100 USD | discount -50 USD reason TEST COMMENT |

    Scenario Outline: absolute fixed discount with since date
        Given formula is discount.since('08.2018').fixed('50 USD')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second           |
            | 2018-07-01 | monthly 100 USD |                  |
            | 2018-07-31 | monthly 100 USD |                  |
            | 2018-08-01 | monthly 100 USD | discount -50 USD |
            | 2018-08-22 | monthly 100 USD | discount -50 USD |
            | 2018-09-01 | monthly 100 USD | discount -50 USD |
            | 2028-11-11 | monthly 100 USD | discount -50 USD |

    Scenario Outline: relative fixed discount with since date and 400$ charge
        Given formula is discount.since('08.2018').fixed('20%')
          And action is server monthly 8 units
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second           |
            | 2018-07-31 | monthly 400 USD |                  |
            | 2018-08-01 | monthly 400 USD | discount -80 USD |
            | 2018-08-22 | monthly 400 USD | discount -80 USD |
            | 2028-11-11 | monthly 400 USD | discount -80 USD |

    Scenario Outline: relative fixed discount with till date
        Given formula is discount.till('09.2018').fixed('20%')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second           |
            | 2012-02-01 | monthly 100 USD | discount -20 USD |
            | 2018-07-31 | monthly 100 USD | discount -20 USD |
            | 2018-08-01 | monthly 100 USD | discount -20 USD |
            | 2018-08-22 | monthly 100 USD | discount -20 USD |
            | 2018-09-01 | monthly 100 USD |                  |
            | 2028-09-11 | monthly 100 USD |                  |
            | 2028-11-11 | monthly 100 USD |                  |

    Scenario Outline: relative fixed discount with since and till date
        Given formula is discount.since('08.2018').till('09.2018').fixed('20%')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second           |
            | 2018-07-31 | monthly 100 USD |                  |
            | 2018-08-01 | monthly 100 USD | discount -20 USD |
            | 2018-08-22 | monthly 100 USD | discount -20 USD |
            | 2018-09-01 | monthly 100 USD |                  |
            | 2028-09-11 | monthly 100 USD |                  |
            | 2028-11-11 | monthly 100 USD |                  |

    Scenario Outline: relative fixed discount with since and term
        Given formula is discount.fixed('20%').since('08.2018').lasts('2 months')
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first           | second           |
            | 2018-07-31 | monthly 100 USD |                  |
            | 2018-08-01 | monthly 100 USD | discount -20 USD |
            | 2018-08-22 | monthly 100 USD | discount -20 USD |
            | 2018-09-01 | monthly 100 USD | discount -20 USD |
            | 2018-10-11 | monthly 100 USD |                  |
            | 2028-11-11 | monthly 100 USD |                  |
