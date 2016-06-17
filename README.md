Codename: Falcon
================

A Symfony project created on March 18, 2016, 2:20 pm.

Clone repo, run migration:
`php bin/console doctrine:migrations:migrate`

To run genres import from litres:
`php bin/console app:update-litres-data genres`

To run books import from litres:
`php bin/console app:update-litres-data books`