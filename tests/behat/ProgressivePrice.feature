Feature: Progressive price fee

  Background:
    Given target progressive price for overuse,cdn_traf95_max is     0.0085 USD per Mbps less 500 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0080 USD per Mbps great_equal 500 Mbps and less 600 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0075 USD per Mbps great_equal 600 Mbps and less 700 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0070 USD per Mbps great_equal 700 Mbps and less_equal 800 Mbps
      And target progressive price for overuse,cdn_traf95_max is     0.0060 USD per Mbps great 800 Mbps
     Then create progressive price

  Scenario Outline: Monthly fee for fraction of month
    Given action is target overuse 720 Mbps in <sale_time>
    Then first charge is <charge>
    Examples:
      | description   | sale_time           | unsale_time         | charge             |
      | A few weeks   | 2024-02-01 11:50:00 | 2024-02-16 15:15:00 | monthly 776.96 USD |
      | A few hours   | 2024-02-10 11:50:00 | 2024-02-10 15:15:00 | monthly 7.31 USD   |
      | A few minutes | 2024-02-10 11:50:00 | 2024-02-10 11:55:00 | monthly 0.18 USD   |
      | Just a second | 2024-02-10 11:50:00 | 2024-02-10 11:50:01 | monthly 0.01 USD   |
