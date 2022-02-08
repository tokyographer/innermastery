<?php
namespace Modules\Location;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Helpers\SitemapHelper;
use Modules\Hotel\Models\Hotel;
use Modules\Location\Models\Location;
use Modules\ModuleServiceProvider;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(SitemapHelper $sitemapHelper){
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        if(is_installed()){
            $sitemapHelper->add("location",[app()->make(Location::class),'getForSitemap']);
        }
    }
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouterServiceProvider::class);
    }


    public static function getAdminMenu()
    {
        return [
        ];
    }
    public static function getTemplateBlocks(){
        return [
            'list_locations'=>"\\Modules\\Location\\Blocks\\ListLocations",
        ];
    }
}
