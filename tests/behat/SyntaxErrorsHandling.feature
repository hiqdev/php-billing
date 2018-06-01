Feature: SyntaxErrorsHandling
    Background:
        Given server monthly price is 50 USD per unit
          And action is server monthly 2 units

    Scenario: syntax error are handled correctly
        Given formula is discount..
         Then error is
           """
           Unexpected token "." (dot) at line 1 and column 9:
           discount..
                   ↑
           """
         When calculating charges

    Scenario: semantics errors are handled correctly
      Given formula is discount.error()
       Then error is Try to call an undefined method: error (dimension number 1 of discount).
       When calculating charges
