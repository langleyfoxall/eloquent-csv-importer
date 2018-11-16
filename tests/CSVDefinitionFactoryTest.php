<?php

use Illuminate\Http\File;
use LangleyFoxall\EloquentCSVImporter\CSVDefinitionFactory;
use LangleyFoxall\EloquentCSVImporter\Models\CSVDefinition;
use LangleyFoxall\EloquentCSVImporter\Tests\TestModel;
use Orchestra\Testbench\TestCase;

class CSVDefinitionFactoryTest extends TestCase
{

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \LangleyFoxall\EloquentCSVImporter\EloquentCSVImporterServiceProvider::class,
        ];
    }

    protected function setUp()
    {
        parent::setUp();
        $this->runMigrations();
    }

    /**
     * Setup the test environment
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->setupDatabase($app);
    }

    /**
     * Test creation of a definition
     */
    public function testDefinitionCreation()
    {
        $definitionMulti = $this->createDefinition(true);
        $this->assertTrue($definitionMulti->exists);
        $this->assertDatabaseHas('csv_definitions', [
            'id' => $definitionMulti->id,
            'name' => 'hello world',
            'description' => 'hello world description',
            'mappings' => "\"{\\\"column1\\\":\\\"column1_map_to\\\",\\\"column2\\\":\\\"column2_map_to\\\",\\\"column3\\\":\\\"column3_map_to\\\",\\\"column4\\\":\\\"column4_map_to\\\"}\""
        ]);

        $definitionSingle = $this->createDefinition(false);
        $this->assertTrue($definitionSingle->exists);
        $this->assertDatabaseHas('csv_definitions', [
            'id' => $definitionSingle->id,
            'name' => 'hello world',
            'description' => 'hello world description',
            'mappings' => "\"{\\\"column1\\\":\\\"column1_map_to\\\"}\""
        ]);
    }

    /**
     * Create a CSV Definition
     * @param $multipleColumns
     * @return CSVDefinition
     * @throws \LangleyFoxall\EloquentCSVImporter\Exceptions\UnknownCSVMappableColumnException
     */
    protected function createDefinition($multipleColumns)
    {
        $factory = (new CSVDefinitionFactory(TestModel::class))
            ->setMeta(['name' => 'hello world', 'description' => 'hello world description']);
        if ($multipleColumns) {
            $factory->mapColumns([
                'column1' => 'column1_map_to',
                'column2' => 'column2_map_to',
                'column3' => 'column3_map_to',
                'column4' => 'column4_map_to'
            ]);
        }else {
            $factory->mapColumn('column1', 'column1_map_to');
        }
        return $factory->create();
    }

    /**
     * Sets up tests to use in-memory SQLite connection
     * @param $app
     */
    protected function setupDatabase($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
    }

    /**
     * Run testing migrations
     */
    protected function runMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database');
    }
}
