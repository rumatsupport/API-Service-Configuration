<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// helpers
use App\Helpers\AuthHelpers;

class BuildingStatus extends Model {
    protected $table = 't_building_status';

    public $incrementing = false;

    public $timestamps = false;

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
