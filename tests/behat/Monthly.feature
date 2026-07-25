Feature: Monthly fee

  Background:
    Given server monthly price is 1488 USD per item

  Scenario Outline: Monthly fee for fraction of month
    Given client rejected service at <unsale_time>
    And action is server monthly 1 item in <sale_time>
    Then first charge is <charge>
    Examples:
      | description   | sale_time           | unsale_time         | charge             |
      | A few weeks   | 2024-02-01 11:50:00 | 2024-02-16 15:15:00 | monthly 776.96 USD |
      | A few hours   | 2024-02-10 11:50:00 | 2024-02-10 15:15:00 | monthly 7.30 USD   |
      | A few minutes | 2024-02-10 11:50:00 | 2024-02-10 11:55:00 | monthly 0.18 USD   |
      | Just a second | 2024-02-10 11:50:00 | 2024-02-10 11:50:01 | monthly 0.01 USD   |
