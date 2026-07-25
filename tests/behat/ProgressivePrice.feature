Feature: Progressive price billing

  Background:
    Given target progressive price for overuse,cdn_traf95_max is   1.0000 USD per Mbps over 50 Mbps
    And target progressive price for overuse,cdn_traf95_max is     0.0180 USD per Mbps over 500 Mbps
    And target progressive price for overuse,cdn_traf95_max is     0.0075 USD per Mbps over 600 Mbps
    And target progressive price for overuse,cdn_traf95_max is     0.0070 USD per Mbps over 700 Mbps
    And target progressive price for overuse,cdn_traf95_max is     0.0065 USD per Mbps over 800 Mbps
    Then build progressive price

  Scenario Outline: Overuse charge for cdn_traf95_max
    Given action is target overuse,cdn_traf95_max <overuse>
    When action date is <date>
    Then first charge is <first>
    And progressive price calculation steps are <explanation>
    Examples:
      | date       | overuse  | first                             | explanation                                               |
      | 2024-01-01 | 1 Gbps   | overuse,cdn_traf95_max 454.55 USD | 200*0.0065 + 100*0.0070 + 100*0.0075 + 100*0.0180 + 450*1 |
      | 2024-01-01 | 720 Mbps | overuse,cdn_traf95_max 452.69 USD | 0*0.0065 + 20*0.0070 + 100*0.0075 + 100*0.0180 + 450*1    |
      | 2024-01-01 | 30 Mbps  | overuse,cdn_traf95_max 0 USD      |                                                           |
      | 2024-01-01 | 50 Mbps  | overuse,cdn_traf95_max 0 USD      |                                                           |
      | 2024-01-01 | 0 Mbps   | overuse,cdn_traf95_max 0 USD      |                                                           |
