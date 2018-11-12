<?php

namespace LangleyFoxall\EloquentCSVImporter\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;
use Illuminate\Support\Collection;
use LangleyFoxall\EloquentCSVImporter\CSVParser;
use LangleyFoxall\EloquentCSVImporter\Exceptions\UnknownCSVMappableColumnException;
use League\Csv\Exception;

/**
 * Class CSVDefinition
 * @package App\Models
 */
class CSVDefinition extends Model
{
    protected $table = 'csv_definitions';
    /**
     * @var array
     */
    protected $casts = [
        'mappings' => 'array',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * Return an instance of the mappable class
     * @return mixed
     */
    protected function getMappable()
    {
        return new $this->mappable_type;
    }

    /**
     * Get the mappings as an array
     * @return mixed
     */
    protected function getMappings()
    {
        return json_decode($this->mappings, true);
    }

    /**
     * Get a valid model mapping for a csv key
     * @param $from
     * @return mixed
     * @throws UnknownCSVMappableColumnException
     */
    protected function getValidToProperty($from)
    {
        $mappings = $this->getMappings();
        \Log::info($from, $mappings);
        if (!array_key_exists($from, $mappings)) {
            throw new UnknownCSVMappableColumnException('Unknown column mapping: '.$from);
        }

        $toKey = $mappings[$from];

        if (!$this->getMappable()::getCSVMappableColumns()->contains($toKey)) {
            throw new UnknownCSVMappableColumnException('Unknown model mapping: '.$toKey);
        }

        return $toKey;
    }

    /**
     * Map either a file or a CSV string to an array of models
     * @param File||String $data
     * @return Collection
     * @throws UnknownCSVMappableColumnException
     * @throws Exception
     */
    protected function instantiateModels($data)
    {
        $csvParser = new CSVParser($data);
        $parserIterator = $csvParser->getIterator();
        $mappedModels = collect();

        $mappings = $this->getMappings();
        $mappingKeys = array_keys($mappings);

        foreach ($parserIterator as $parsedRow) {
            $model = $this->getMappable();
            foreach ($mappingKeys as $mapFrom) {
                $CSVValue = $parsedRow[$mapFrom];
                $toKey = $this->getValidToProperty($mapFrom);
                $model->setAttribute($toKey, $CSVValue);
            }
            $mappedModels->push($model);
        }

        return $mappedModels;
    }

    /**
     * Parse a csv file or string and return a collection of models
     * @param $data
     * @return Collection
     * @throws Exception
     * @throws UnknownCSVMappableColumnException
     */
    public function makeModels($data)
    {
        return $this->instantiateModels($data);
    }

    /**
     * Parse a csv file or string and return a collection of models that have been saved to the Database
     * @param $data
     * @return Collection
     * @throws Exception
     * @throws UnknownCSVMappableColumnException
     * @throws \Throwable
     */
    public function createModels($data)
    {
        $models = $this->instantiateModels($data);
        DB::transaction(function () use ($models) {
            $models->each->save();
        });

        return $models;
    }

    /**
     * Model that the definition is associated with
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function assigned()
    {
        return $this->morphTo();
    }
}
