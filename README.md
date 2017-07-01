Codename: Falcon
================

Clone repo, run migration:
`php bin/console doctrine:migrations:migrate`

To run genres import from litres:
`php bin/console app:update-litres-data genres`

To run books import from litres:
`php bin/console app:update-litres-data books`

To run update of menu featured books:
`php bin/console app:update-featured-menu`