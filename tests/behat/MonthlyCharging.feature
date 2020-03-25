Feature: Monthly charging
    Background:
        Given tariff T100 period is month
          And T100 license fee is 100 USD
        Given tariff T200 period is month
          And T200 license fee is 200 USD

    Scenario Outline: whole month
        Given subscription is tariff T100
          And subscription is created on 2020-02-01
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first                        | second                               |
            | 2020-01-01 |                              |                                      |
            | 2020-02-01 | license 100 USD quantity 1.0 |                                      |
            | 2020-03-01 | license 100 USD quantity 1.0 |                                      |
            | 2020-04-01 | license 100 USD quantity 1.0 |                                      |
            | 2028-11-11 | license 100 USD quantity 1.0 |                                      |

    Scenario Outline: part of month: created when 1/5 month left, closed when 1/2 passed
        Given subscription is tariff T100
          And subscription is created on 2020-02-25
          And subscription is closed on 2020-04-16
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first                        | second                               |
            | 2020-01-01 |                              |                                      |
            | 2020-02-01 | license  20 USD quantity 0.2 |                                      |
            | 2020-03-01 | license 100 USD quantity 1.0 |                                      |
            | 2020-04-01 | license  50 USD quantity 0.5 |                                      |
            | 2020-05-01 |                              |                                      |
            | 2028-11-11 |                              |                                      |

    Scenario Outline: subscription closed and reopened again
        Given subscription is tariff T100
          And subscription is created on 2020-02-07
         Then subscription is closed on  2020-02-13
          And subscription is created on 2020-02-19
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first                        | second                               |
            | 2020-01-01 |                              |                                      |
            | 2020-02-01 | license  20 USD quantity 0.2 | license  40 USD quantity 0.4         |
            | 2020-03-01 | license 100 USD quantity 1.0 |                                      |
            | 2020-04-01 | license 100 USD quantity 1.0 |                                      |
            | 2020-05-01 | license 100 USD quantity 1.0 |                                      |
            | 2028-11-11 | license 100 USD quantity 1.0 |                                      |

    Scenario Outline: changed tariff: 1/5 month T100, 3/5 month T200
        Given subscription is tariff T100
          And subscription is created on 2020-02-07
         Then subscription is tariff T200
          And subscription is created on 2020-02-13
         When action date is <date>
         Then first charge is <first>
          And second charge is <second>
        Examples:
            | date       | first                        | second                               |
            | 2020-01-01 |                              |                                      |
            | 2020-02-01 | license  20 USD quantity 0.2 | license 120 USD quantity 0.6         |
            | 2020-03-01 | license 200 USD quantity 1.0 |                                      |
            | 2020-04-01 | license 200 USD quantity 1.0 |                                      |
            | 2020-05-01 | license 200 USD quantity 1.0 |                                      |
            | 2028-11-11 | license 200 USD quantity 1.0 |                                      |

