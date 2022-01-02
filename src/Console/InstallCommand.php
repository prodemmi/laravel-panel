<?php

namespace Prodemmi\Lava\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'lava:install';

    protected $description = 'Install the Lava';

    public function handle()
    {
        $this->info( 'Installing Lava...' );

        $this->info( 'Publishing configuration...' );

        $this->publishConfig();

        $this->info( 'Publishing provider...' );

        $this->publishServiceProvider();

        $this->info( 'Installed Lava' );
    }

    private function configExists($fileName)
    {
        return File::exists( config_path( $fileName ) );
    }

    private function providerExists($fileName)
    {
        return File::exists( app_path( "Providers/$fileName" ) );
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm( 'Config file already exists. Do you want to overwrite it?', FALSE );
    }

    private function publishWithTag($tag, $forcePublish = FALSE)
    {
        $params = [
            '--provider' => "Prodemmi\Lava\LavaServiceProvider",
            '--tag'      => "lava-$tag"
        ];

        if ( $forcePublish === TRUE ) {
            $params['--force'] = TRUE;
        }

        $this->call( 'vendor:publish', $params );
    }

    private function publishConfig()
    {

        if ( !$this->configExists( 'lava.php' ) ) {
            $this->publishWithTag( 'config' );
            $this->info( 'Published configuration' );
        }
        else {
            if ( $this->shouldOverwriteConfig() ) {
                $this->info( 'Overwriting configuration file...' );
                $this->publishWithTag( 'config', TRUE );
            }
            else {
                $this->info( 'Existing configuration was not overwritten' );
            }
        }

    }

    private function publishServiceProvider()
    {

        if ( !$this->providerExists( 'LavaServiceProvider.php' ) ) {
            $this->publishWithTag( 'provider' );
            $this->info( 'Published provider' );
        }
        else {
            if ( $this->shouldOverwriteConfig() ) {
                $this->info( 'Overwriting provider file...' );
                $this->publishWithTag( 'provider', TRUE );
            }
            else {
                $this->info( 'Existing provider was not overwritten' );
            }
        }

    }
}