<?php

namespace LangleyFoxall\EloquentCSVImporter;

use Illuminate\Http\File;
use League\Csv\Exception;
use League\Csv\Reader;

class CSVParser
{
    protected $reader;

    /**
     * CSVParser constructor.
     * @param $data
     * @param $headerOffset
     * @throws Exception
     */
    public function __construct($data, $headerOffset = 0)
    {
        if ($data instanceof File) {
            $this->reader = Reader::createFromFileObject($data->openFile());
        }

        if (is_string($data)) {
            $this->reader = Reader::createFromString($data);
        }

        $this->reader->setHeaderOffset($headerOffset);
    }

    /**
     * Get the CSV data as an iterator
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->reader->getRecords();
    }
}
