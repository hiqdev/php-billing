Feature: Progressive price fee

  Background:
    Given target progressive price for overuse,cdn_traf95_max is     0.0085 USD per Mbps great 0 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0080 USD per Mbps great_equal 500 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0075 USD per Mbps great_equal 600 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0070 USD per Mbps great_equal 700 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0060 USD per Mbps great 800 Mbps
     Then create progressive price

  Scenario Outline: Monthly fee for fraction of month
    Given action is target overuse,cdn_traf95_max <overuse>
     When action date is <date>
     Then first charge is <first>
    Examples:
      | date       | overuse   | first                            |
      | 2024-01-01 | 720 Mbps  | overuse,cdn_traf95_max 5.93 USD  |
