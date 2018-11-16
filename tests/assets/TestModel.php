<?php

namespace LangleyFoxall\EloquentCSVImporter\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use LangleyFoxall\EloquentCSVImporter\Traits\CSVMappable;

class TestModel extends Model
{
    use CSVMappable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
