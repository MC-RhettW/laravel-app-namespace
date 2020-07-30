<?php
/**
 * AppNamespaceSupport\ServiceProvider.php
 *
 * @author     RhettW
 * @package    mcdev/php
 * @subpackage support/illuminate
 * @mc-cblog <<<
 *  =================================================================
 * TITLE:           AppNamespaceSupport\ServiceProvider.php
 * DESCRIPTION:     Service provider to register the the app:name with the service container
 *
 * DATE                 USER            DESC
 * ------------------------------------------------------------------
 * 2020-07-16           RhettW          Initial creation; Clone FOSS package by Andrey Helldar
 *
 * ===================================================================
 * >>>
 */

namespace MCDev\AppNamespaceSupport;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use MCDev\AppNamespaceSupport\Commands\AppNamespaceCommand as Command;


/**
 * @method commands( string[] $array )
 */
class ServiceProvider extends IlluminateServiceProvider
{
    protected $defer = false;
    
    public function boot ()
    {
        if ( $this->app->runningInConsole() ) $this->commands( [
          Command::class,
        ] );
    }
}
