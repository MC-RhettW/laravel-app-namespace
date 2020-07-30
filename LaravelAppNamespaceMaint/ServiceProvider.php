<?php
/**
 * LaravelAppNamespace\ServiceProvider.php
 * @author RhettW
 * @package MCDev
 * @subpackage LaravelSupport
 * @mc-cblog <<<
 *  =================================================================
 * TITLE:           LaravelAppNamespace\ServiceProvider.php
 * DESCRIPTION:     Service provider to register the the app:name with the service container
 *
 * DATE                 USER            DESC
 * ------------------------------------------------------------------
 * 2020-07-16           RhettW          Initial creation; Clone FOSS package by Andrey Helldar
 *
 * ===================================================================
 * >>>
*/

namespace MCDev\LaravelAppNamespaceMaint;

use MCDev\LaravelAppNamespaceMaint\Commands\AppNamespaceCommand;


/**
 * @method commands( string[] $array )
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = false;
    
    public function boot ()
    {
        if ( $this->app->runningInConsole() ) $this->commands( [
          AppNamespaceCommand::class,
        ] );
    }
}
