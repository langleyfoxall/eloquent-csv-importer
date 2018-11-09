<?php

namespace LangleyFoxall\EloquentCSVImporter\Traits;

trait HasCSVDefinitions
{
    /**
     * Get CSVDefinitions tied to the model
     * @return mixed
     */
    public function CSVDefinitions()
    {
        return $this->morphMany('LangleyFoxall\EloquentCSVImporter\Models\CSVDefinition', 'assigned');
    }
}
