<?php
/**
 * LaravelAppNamespace\Commands\AppName.php.
 * @author RhettW
 * @package MCDev
 * @subpackage LaravelSupport
 * @mc-cblog <<<
 *  =================================================================
 * TITLE:           LaravelAppNamespace\Commands\AppName.php
 * DESCRIPTION:	    Command class providing app:name command for Laravel application
 *
 * DATE                 USER            DESC
 * ------------------------------------------------------------------
 * 2020-07-16           RhettW          Initial creation; Clone FOSS package by Andrey Helldar
 * 2020-07-16           RhettW          Add validation to namespace argument value
 *
 * ===================================================================
 * >>>
*/

namespace MCDev\LaravelAppNamespace\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException as FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use Symfony\Component\String\Exception\InvalidArgumentException as InvalidArgumentException;

/**
 * @property  laravel
 * @method argument(string $string)
 */
class AppName extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'app:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application namespace';

    /**
     * The Composer class instance.
     *
     * @var Composer
     */
    protected $composer;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Current root application namespace.
     *
     * @var string
     */
    protected $currentRoot;

    /**
     * Create a new key generator command.
     *
     * @param Composer   $composer
     * @param Filesystem $files
     */
    public function __construct( Composer $composer, Filesystem $files )
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    public function handle()
    {

        if ( !$this->validateNamespace() )
            throw new InvalidArgumentException( 'Invalid namespace provided to app:name command.' );

        $this->currentRoot = trim( $this->laravel->getNamespace(), '\\' );

        $this->setAppDirectoryNamespace();
        $this->setBootstrapNamespaces();
        $this->setConfigNamespaces();
        $this->setComposerNamespace();
        $this->setDatabaseFactoryNamespaces();

        $this->info( 'Application namespace set!' );

        $this->composer->dumpAutoloads();

        $this->call( 'optimize:clear' );

    }

    /**
     * Check the provided namespace for invalid characters
     *
     * @return bool
     */
    protected function validateNamespace()
    {
        return preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $this->argument( 'name' ) ) == 1;
    }

    /**
     * Set the namespace on the files in the app directory.
     *
     * @throws FileNotFoundException
     */
    protected function setAppDirectoryNamespace()
    {
        $files = Finder::create()
                       ->in( $this->laravel['path'] )
                       ->contains( $this->currentRoot )
                       ->name( '*.php' );

        foreach ( $files as $file ) $this->replaceNamespace( $file->getRealPath() );
    }

    /**
     * Replace the App namespace at the given path.
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     */
    protected function replaceNamespace( $path )
    {
        $search = [
            'namespace ' . $this->currentRoot . ';',
            $this->currentRoot . '\\',
        ];

        $replace = [
            'namespace ' . $this->argument( 'name' ) . ';',
            $this->argument( 'name' ) . '\\',
        ];

        $this->replaceIn( $path, $search, $replace );
    }

    /**
     * Replace the given string in the given file.
     *
     * @param string       $path
     * @param array|string $search
     * @param array|string $replace
     *
     * @throws FileNotFoundException
     */
    protected function replaceIn( $path, $search, $replace )
    {
        if ( $this->files->exists( $path ) )
            $this->files->put( $path, str_replace( $search, $replace, $this->files->get( $path ) ) );
    }

    /**
     * Set the bootstrap namespaces.
     *
     * @throws FileNotFoundException
     */
    protected function setBootstrapNamespaces()
    {
        $search = [
            $this->currentRoot . '\\Http',
            $this->currentRoot . '\\Console',
            $this->currentRoot . '\\Exceptions',
        ];

        $replace = [
            $this->argument( 'name' ) . '\\Http',
            $this->argument( 'name' ) . '\\Console',
            $this->argument( 'name' ) . '\\Exceptions',
        ];

        $this->replaceIn( $this->getBootstrapPath(), $search, $replace );
    }

    /**
     * Get the path to the bootstrap/app.php file.
     *
     * @return string
     */
    protected function getBootstrapPath()
    {
        return $this->laravel->bootstrapPath() . '/app.php';
    }

    /**
     * Set the namespace in the appropriate configuration files.
     *
     * @throws FileNotFoundException
     */
    protected function setConfigNamespaces()
    {
        $this->setAppConfigNamespaces();
        $this->setAuthConfigNamespace();
        $this->setServicesConfigNamespace();
    }

    /**
     * Set the application provider namespaces.
     *
     * @throws FileNotFoundException
     */
    protected function setAppConfigNamespaces()
    {
        $search = [
            $this->currentRoot . '\\Providers',
            $this->currentRoot . '\\Http\\Controllers\\',
        ];

        $replace = [
            $this->argument( 'name' ) . '\\Providers',
            $this->argument( 'name' ) . '\\Http\\Controllers\\',
        ];

        $this->replaceIn( $this->getConfigPath( 'app' ), $search, $replace );
    }

    /**
     * Get the path to the given configuration file.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getConfigPath( $name )
    {
        return $this->laravel['path.config'] . '/' . $name . '.php';
    }

    /**
     * Set the authentication User namespace.
     *
     * @throws FileNotFoundException
     */
    protected function setAuthConfigNamespace()
    {
        $this->replaceIn(
            $this->getConfigPath( 'auth' ),
            $this->currentRoot . '\\User',
            $this->argument( 'name' ) . '\\User'
        );
    }

    /**
     * Set the services User namespace.
     *
     * @throws FileNotFoundException
     */
    protected function setServicesConfigNamespace()
    {
        $this->replaceIn(
            $this->getConfigPath( 'services' ),
            $this->currentRoot . '\\User',
            $this->argument( 'name' ) . '\\User'
        );
    }

    /**
     * Set the PSR-4 namespace in the Composer file.
     *
     * @throws FileNotFoundException
     */
    protected function setComposerNamespace()
    {
        $this->replaceIn(
            $this->getComposerPath(),
            str_replace( '\\', '\\\\', $this->currentRoot ) . '\\\\',
            str_replace( '\\', '\\\\', $this->argument( 'name' ) ) . '\\\\'
        );
    }

    /**
     * Get the path to the Composer.json file.
     *
     * @return string
     */
    protected function getComposerPath()
    {
        return base_path( 'composer.json' );
    }

    /**
     * Set the namespace in database factory files.
     *
     * @throws FileNotFoundException
     */
    protected function setDatabaseFactoryNamespaces()
    {
        $files = Finder::create()
                       ->in( database_path( 'factories' ) )
                       ->contains( $this->currentRoot )
                       ->name( '*.php' );

        foreach ( $files as $file ) $this->replaceIn(
            $file->getRealPath(),
            $this->currentRoot,
            $this->argument( 'name' )
        );

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The desired namespace'],
        ];
    }
}
