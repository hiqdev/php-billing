# Agent Instructions — php-billing

Pure billing domain library. No framework dependency — no Yii2, no config-plugin.

@docs/domain-model.md
@docs/price-types.md

## Key domain concepts

Customer → Plan → Price → Action → Charge → Bill.
Sale represents a subscription binding a Customer to a Target under a Plan.

## Rules

No framework imports allowed — this is a pure domain library.
Price types (RatePrice, SinglePrice, EnumPrice, ProgressivePrice) implement pricing strategies.
Formula classes define calculation rules applied to prices.
Money is a value object — never use floats for monetary values.
