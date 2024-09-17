<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// helpers
use App\Helpers\Helpers;

class Regional extends Model {
    protected $table = 'mt_regional';

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public $timestamps = false;

    public function getUniqueID()
    {
        $config = [
            'table' => $this->table,
            'prefix' => 'REG-',
            'length' => 4
        ];

        return Helpers::generateUniqueID($config);
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    // unit
    public function unit()
    {
        return $this->hasMany(Unit::class, 'regional_id', 'id');
    }

    // asmen
    public function asmen()
    {
        return $this->belongsTo(Asmen::class, 'asmen_area_id', 'id');
    }
}
