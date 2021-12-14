<?php

namespace Realtydev\StatamicDatabase;

use Statamic\Contracts\Assets\AssetRepository as AssetRepositoryContract;
use Realtydev\StatamicDatabase\Assets\AssetRepository;
use Statamic\Contracts\Assets\AssetContainerRepository as AssetContainerRepositoryContract;
use Realtydev\StatamicDatabase\Assets\AssetContainerRepository;
use Statamic\Contracts\Entries\CollectionRepository as CollectionRepositoryContract;
use Realtydev\StatamicDatabase\Entries\CollectionRepository;
use Statamic\Contracts\Structures\CollectionTreeRepository as CollectionTreeRepositoryContract;
use Realtydev\StatamicDatabase\Trees\CollectionTreeRepository;
use Statamic\Contracts\Entries\EntryRepository as EntryRepositoryContract;
use Realtydev\StatamicDatabase\Entries\EntryRepository;
use Statamic\Contracts\Forms\FormRepository as FormRepositoryContract;
use Realtydev\StatamicDatabase\Forms\FormRepository;
use Statamic\Contracts\Globals\GlobalRepository as GlobalRepositoryContract;
use Realtydev\StatamicDatabase\Globals\GlobalRepository;
use Statamic\Contracts\Structures\NavigationRepository as NavigationRepositoryRepository;
use Realtydev\StatamicDatabase\Navigation\NavigationRepository;
use Statamic\Contracts\Structures\NavTreeRepository as NavTreeRepositoryContract;
use Realtydev\StatamicDatabase\Trees\NavTreeRepository;
use Statamic\Contracts\Taxonomies\TaxonomyRepository as TaxonomyRepositoryContract;
use Realtydev\StatamicDatabase\Taxonomies\TaxonomyRepository;
use Statamic\Contracts\Taxonomies\TermRepository as TermRepositoryContract;
use Realtydev\StatamicDatabase\Taxonomies\TermRepository;

use Statamic\Contracts\Auth\RoleRepository as RolesRepositoryContract;
use Realtydev\StatamicDatabase\Roles\RolesRepository;

use Realtydev\StatamicDatabase\Commands\FileMigration;
use Illuminate\Support\ServiceProvider;
use Statamic\Statamic;

class StatamicDatabaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/statamic-database.php' => config_path('statamic/database.php'),
        ], 'statamic-database-config');

        $this->publishes([
            __DIR__.'/Database/Seeders/DefaultBlueprintSeeder.php' => database_path('seeders/DefaultBlueprintSeeder.php'),
        ], 'statamic-database-seeders');

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->commands([FileMigration::class]);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/statamic-database.php', 'statamic.database');

        $config = config('statamic.database');

        if ($config['assets']) {
            Statamic::repository(AssetRepositoryContract::class, AssetRepository::class);
            Statamic::repository(AssetContainerRepositoryContract::class, AssetContainerRepository::class);
        }

        if ($config['collections']) {
            Statamic::repository(CollectionRepositoryContract::class, CollectionRepository::class);
            Statamic::repository(CollectionTreeRepositoryContract::class, CollectionTreeRepository::class);
        }

        if ($config['entries']) {
            Statamic::repository(EntryRepositoryContract::class, EntryRepository::class);
        }

        if ($config['forms']) {
            Statamic::repository(FormRepositoryContract::class, FormRepository::class);
        }

        if ($config['globals']) {
            Statamic::repository(GlobalRepositoryContract::class, GlobalRepository::class);
        }

        if ($config['navigation']) {
            Statamic::repository(NavigationRepositoryRepository::class, NavigationRepository::class);
            Statamic::repository(NavTreeRepositoryContract::class, NavTreeRepository::class);
        }

        if ($config['taxonomies']) {
            Statamic::repository(TaxonomyRepositoryContract::class, TaxonomyRepository::class);
            Statamic::repository(TermRepositoryContract::class, TermRepository::class);
        }

        if ($config['roles']) {
            Statamic::repository(RolesRepositoryContract::class, RolesRepository::class);
        }
    }
}
