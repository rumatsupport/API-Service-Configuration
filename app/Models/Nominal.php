<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// helpers
use App\Helpers\Helpers;

class Nominal extends Model {
    protected $table = 'mt_nominal';

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public $timestamps = false;

    public function getUniqueID()
    {
        $config = [
            'table' => $this->table,
            'prefix' => 'NOM-',
            'length' => 4
        ];

        return Helpers::generateUniqueID($config);
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
