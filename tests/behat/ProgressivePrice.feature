Feature: Progressive price fee

  Background:
    Given target progressive price for overuse,cdn_traf95_max is     0.0085 USD per Mbps over 0 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0080 USD per Mbps over 500 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0075 USD per Mbps over 600 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0070 USD per Mbps over 700 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0065 USD per Mbps over 800 Mbps
     Then create progressive price

  Scenario Outline: Monthly fee for fraction of month
    Given action is target overuse,cdn_traf95_max <overuse>
     When action date is <date>
     Then first charge is <first>
    Examples:
      | date       | overuse   | first                            |
      | 2024-01-01 | 720 Mbps  | overuse,cdn_traf95_max 5.94 USD  |
