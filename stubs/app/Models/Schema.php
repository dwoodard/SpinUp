<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Venturecraft\Revisionable\RevisionableTrait;


class Schema extends Model
{
  use HasFactory;
  use RevisionableTrait;

  protected $revisionEnabled = true;
  protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
  protected $historyLimit = 20; //Maintain a maximum of 20 revisions per model

  public $timestamps = false;

  protected $guarded = [];

  //Mutators
  // Schema
  protected function getSchemaAttribute()
  {
    return json_decode($this->attributes['schema']);
  }
  protected function setSchemaAttribute($value)
  {
    $this->attributes['schema'] = json_encode($value);
  }

  // Model
  protected function getModelAttribute()
  {
    return json_decode($this->attributes['model']);
  }
  protected function setModelAttribute($value)
  {
    $this->attributes['model'] = json_encode($value);
  }

  // Options
  protected function getOptionsAttribute()
  {
    return json_decode($this->attributes['options']);
  }
  protected function setOptionsAttribute($value)
  {
    $this->attributes['options'] = json_encode($value);
  }



  /**
   * Scope a query to only exclude specific Columns.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   *
   * usage: Schema::exclude(['id', 'name'])->get();
   * @author Manojkiran.A <manojkiran10031998@gmail.com>
   */
  public function scopeExclude($query, ...$columns)
  {
    if ($columns !== []) {
      if (count($columns) !== count($columns, COUNT_RECURSIVE)) {
        $columns = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($columns)));
      }

      return $query->select(array_diff($this->getTableColumns(), $columns));
    }
    return $query;
  }

  /**
   * Shows All the columns of the Corresponding Table of Model
   *
   * @return array
   **@author Manojkiran.A <manojkiran10031998@gmail.com>
   * If You need to get all the Columns of the Model Table.
   * Useful while including the columns in search
   */
  public function getTableColumns()
  {
    return \Illuminate\Support\Facades\Cache::rememberForever('MigrMod:' . filemtime(database_path('migrations')), function () {
      return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    });
  }


}

