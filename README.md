## Statamic Database Driver

An eloquent driver for Statamic V3 which supports:

 - Asset Containers
 - Blueprints
 - Collections
 - Entries
 - Fieldsets
 - Forms / Form Submissions
 - Global Sets
 - Navigation
 - Taxonomies/Terms
 - Trees

## Installation

From a standard Statamic V3 site, you can run:
`composer require realtydev/statamic-database`

Run migrations:
`php please migrate`

Then in the register function of your AppServiceProvider, add:
```
public function register()
{
    $this->app->singleton(
        'Statamic\Fields\BlueprintRepository',
        'Realtydev\StatamicDatabase\Blueprints\BlueprintRepository'
    );

    $this->app->singleton(
        'Statamic\Fields\FieldsetRepository',
        'Realtydev\StatamicDatabase\Fieldsets\FieldsetRepository'
    );
}
```
And that should be it!

## Issues/Things to work on

 - No tests.
 - Still needs user roles/groups adding.
 - No real world testing done yet, so probably some more to be added.

## Credits

Thanks to [@statamic](https://statamic.dev/)  for creating the entries part of this in [statamic/eloquent-driver](https://github.com/statamic/eloquent-driver), which a lot of this was based off.
