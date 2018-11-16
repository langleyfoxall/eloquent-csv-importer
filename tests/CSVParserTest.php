<?php

use LangleyFoxall\EloquentCSVImporter\CSVParser;
use Illuminate\Http\File;
use Orchestra\Testbench\TestCase;

class CSVParserTest extends TestCase
{

    /**
     * Test the CSV Parser
     * @throws \League\Csv\Exception
     */
    public function testSuccessfulParseString()
    {
        $file = file_get_contents(__DIR__ . '/assets/valid.csv');
        $parser = new CSVParser($file);
        $iterator = $parser->getIterator();

        $this->assertIterator($iterator);
    }

    public function testSuccessfulParseFile()
    {
        $file = new File(__DIR__ . '/assets/valid.csv');
        $parser = new CSVParser($file);
        $iterator = $parser->getIterator();

        $this->assertIterator($iterator);
    }

    /**
     * Assertions about the CSV parser iterator
     * @param Iterator $iterator
     */
    protected function assertIterator(Iterator $iterator)
    {
        $this->assertInstanceOf(\Iterator::class, $iterator);

        $values = iterator_to_array($iterator);
        $this->assertEquals([
            'column1' => 'hello world',
            'column2' => 'hello world 2',
            'column3' => 'hello world 3',
            'column4' => '12.50',
        ], $values[1]);
    }

}
