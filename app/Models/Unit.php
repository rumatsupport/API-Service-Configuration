<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// helpers
use App\Helpers\Helpers;

class Unit extends Model {
    protected $table = 'mt_unit';

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public $timestamps = true;

    public function getUniqueID()
    {
        $config = [
            'table' => $this->table,
            'prefix' => 'UNT-',
            'length' => 5
        ];

        return Helpers::generateUniqueID($config);
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    // regional
    public function regional()
    {
        return $this->belongsTo(Regional::class, 'regional_id');
    }

    // building
    public function building()
    {
        return $this->hasOne(Building::class, 'unit_id');
    }

    // rute
    public function rute()
    {
        return $this->belongsTo(Rute::class, 'rute_id');
    }
}
