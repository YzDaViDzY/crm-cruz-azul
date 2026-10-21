<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication {
        createApplication as private createBaseApplication;
    }

    /**
     * Crea la aplicación y aborta antes de tocar ninguna base de datos si la
     * configuración de tests no es exactamente SQLite en memoria.
     *
     * Se ejecuta antes de RefreshDatabase, por lo que ni siquiera se llega a
     * lanzar migrate:fresh contra una conexión incorrecta (por ejemplo MySQL).
     */
    public function createApplication()
    {
        $app = $this->createBaseApplication();

        $connection = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.{$connection}.database");

        if ($app->environment() !== 'testing' || $connection !== 'sqlite' || $database !== ':memory:') {
            throw new \RuntimeException(
                'Tests abortados: deben ejecutarse con APP_ENV=testing y SQLite en memoria '
                .'(DB_CONNECTION=sqlite, DB_DATABASE=:memory:). Revisa phpunit.xml.'
            );
        }

        return $app;
    }
}
