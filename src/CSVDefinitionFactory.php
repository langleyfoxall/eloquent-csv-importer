<?php

namespace LangleyFoxall\EloquentCSVImporter;

use Illuminate\Support\Collection;

use LangleyFoxall\EloquentCSVImporter\Models\CSVDefinition;
use LangleyFoxall\EloquentCSVImporter\Exceptions\UnknownCSVMappableColumnException;

class CSVDefinitionFactory
{

    protected $mappable;
    protected $meta;
    protected $maps = [];

    /**
     * CSVDefinitionFactory constructor.
     * @param $mappable
     */
    public function __construct($mappable)
    {
        $this->mappable = $mappable;
    }

    /**
     * Get the mappable columns
     * @return Collection
     */
    protected function getMappableColumns()
    {
        return call_user_func([$this->mappable, 'getCSVMappableColumns']);
    }

    /**
     * Get the encoded maps ready to be saved to the DB
     * @return false|string
     */
    protected function getJSONMappings()
    {
        return json_encode($this->maps);
    }

    /**
     * Instantiate a new definitions model
     * @return CSVDefinition
     */
    protected function newDefinitionsModel()
    {
        $csvDefinition = new CSVDefinition();
        $csvDefinition->mappable_type = $this->mappable;
        $csvDefinition->mappings = $this->getJSONMappings();

        foreach ($this->meta as $key => $value) {
            $csvDefinition->setAttribute($key, $value);
        }

        return $csvDefinition;
    }

    protected function validMaps($maps) {
        $arrayValues = array_values($maps);
        return !empty(array_intersect_key($arrayValues, $this->getMappableColumns()->toArray()));
    }

    /**
     * Set the metadata of the CSVDefinition - things like names, descriptions etc
     * @param array $attributes
     * @return $this;
     */
    public function setMeta($attributes)
    {
        $this->meta = $attributes;
        return $this;
    }

    /**
     * Map a column to a property;
     * @param $from
     * @param $to
     * @return $this;
     * @throws UnknownCSVMappableColumnException
     */
    public function mapColumn($from, $to)
    {
        if (!$this->getMappableColumns()->contains($to)) {
            throw new UnknownCSVMappableColumnException('Unknown column mapping: '.$to);
        }
        $this->maps[$from] = $to;
        return $this;
    }

    /**
     * Map columns
     * @param $array
     * @return $this
     * @throws UnknownCSVMappableColumnException
     */
    public function mapColumns($array)
    {
        if (!$this->validMaps($array)) {
            throw new UnknownCSVMappableColumnException("Unknown column mapping in array");
        }
        $this->maps = $array;
        return $this;
    }

    /**
     * make a new CSVDefinition
     * @return CSVDefinition $csvDefinition
     */
    public function make()
    {
        return $this->newDefinitionsModel();
    }

    /**
     * Make a new CSVDefinition & save it to the DB
     */
    public function create()
    {
        $definition = $this->newDefinitionsModel();
        $definition->save();
        return $definition;
    }
}
