This billing is designed to be flexible and abstract, so supports different use cases.
We use this package in production, wrapping it additional layers, such as:

1. Plan and Price storage and management UI for managers, so they can create plans,
   fill them with prices and assign to Customers.
2. Actions and Orders producer. This layer takes end-user actions
   (such as purchasing something) and produces the right Actions inside the Order
3. Persistence layer. This layer implements various RepositoryInterfaces,
   defined in this package (such as PlanRepositoryInterface,
   providing data saving and retrieving logic for the required entities.
4. Periodic operations (CRON tasks). This includes meters fetching
   (such as accumulated resources consumption), transforming them to actions
   with the right quantity, running billing on them, updating Bills and their Charges.
5. Business metrics monitoring, analysis and alerting.
   This layer provides regular checks over data, produces by the billing in order to ensure system health.
6. Read API. This API accepts requests, fetches data from the DBMS
   and implements search with filtering, ordering, access control
   and more for Orders, Actions, Bills, Prices and so on.

So, as you can see, this package is a concrete foundation of big billing system,
but it requires a lot of bricks on top of it to become a fully operable billing.
Unfortunately, we do not have all those bricks open-sourced and documented
because many of them implement customer-specific logic that cannot be disclosed.

