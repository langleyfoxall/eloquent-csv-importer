<?php

use Illuminate\Http\File;
use LangleyFoxall\EloquentCSVImporter\CSVDefinitionFactory;
use LangleyFoxall\EloquentCSVImporter\Models\CSVDefinition;
use LangleyFoxall\EloquentCSVImporter\Tests\TestModel;
use Orchestra\Testbench\TestCase;

class CSVDefinitionTest extends TestCase
{
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
     * Test the creation of models from a definition
     */
    public function testModelCreation()
    {
        $file = file_get_contents(__DIR__ . '/assets/valid.csv');
        $definitionMulti = $this->createDefinition(true);
        $modelsMulti = $definitionMulti->createModels($file, []);

        $this->assertDatabaseHas('test_models', [
            'id' => $modelsMulti[0]->id,
            'column1_map_to' => 'hello world',
            'column2_map_to' => 'hello world 2',
            'column3_map_to' => 'hello world 3',
            'column4_map_to' => '12.5',
        ]);

        $definitionSingle = $this->createDefinition(false);
        $modelsSingle = $definitionSingle->createModels($file, []);

        $this->assertDatabaseHas('test_models', [
            'id' => $modelsSingle[0]->id,
            'column1_map_to' => 'hello world',
        ]);
    }

    /**
     * Test the creation of models from a definition, with a data item manipulator
     */
    public function testModelCreationWithDataItemManipulator()
    {
        $file = file_get_contents(__DIR__ . '/assets/valid.csv');
        $definitionMulti = $this->createDefinition(true);

        $definitionMulti->setDataItemManipulator(function($key, $value) {
            return strtoupper($value);
        });

        $modelsMulti = $definitionMulti->createModels($file, []);

        $this->assertDatabaseHas('test_models', [
            'id' => $modelsMulti[0]->id,
            'column1_map_to' => 'HELLO WORLD',
            'column2_map_to' => 'HELLO WORLD 2',
            'column3_map_to' => 'HELLO WORLD 3',
            'column4_map_to' => '12.5',
        ]);

        $definitionSingle = $this->createDefinition(false);

        $definitionSingle->setDataItemManipulator(function($key, $value) {
            return strtoupper($value);
        });

        $modelsSingle = $definitionSingle->createModels($file, []);

        $this->assertDatabaseHas('test_models', [
            'id' => $modelsSingle[0]->id,
            'column1_map_to' => 'HELLO WORLD',
        ]);
    }

    /**
     * Tests the updating of
     */
    public function testModelCreateUpdate()
    {
        $file = file_get_contents(__DIR__ . '/assets/valid.csv');
        $definitionMulti = $this->createDefinition(true);

        $modelsMulti = $definitionMulti->createModels($file, []);

        $this->assertDatabaseHas('test_models', [
            'id' => $modelsMulti[0]->id,
            'column1_map_to' => 'hello world',
            'column2_map_to' => 'hello world 2',
            'column3_map_to' => 'hello world 3',
            'column4_map_to' => '12.5',
        ]);

        $updateFile = file_get_contents(__DIR__ . '/assets/valid-update.csv');
        //Update where the mapping for column1 is equal to it's value in the csv file
        // Update where column1_map_to = hello world etc
        $updatedModules = $definitionMulti->createModels($updateFile, ['column1']);

        $this->assertDatabaseHas('test_models', [
            'id' => $updatedModules[0]->id,
            'column4_map_to' => '15.0',
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
                'column4' => 'column4_map_to',
            ]);
        } else {
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
