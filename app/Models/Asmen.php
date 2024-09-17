<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// helpers
use App\Helpers\Helpers;

class Asmen extends Model {
    protected $table = 'mt_asmen_area';

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public $timestamps = false;

    public function getUniqueID()
    {
        $config = [
            'table' => $this->table,
            'prefix' => 'ASM-',
            'length' => 4
        ];

        return Helpers::generateUniqueID($config);
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    // regional
    public function regional()
    {
        return $this->hasMany(Regional::class, 'asmen_id', 'id');
    }
}
