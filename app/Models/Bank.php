<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// helpers
use App\Helpers\Helpers;

class Bank extends Model {
    protected $table = 'mt_bank';

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public $timestamps = false;

    public function getUniqueID()
    {
        $config = [
            'table' => $this->table,
            'prefix' => 'BK-',
            'length' => 4
        ];

        return Helpers::generateUniqueID($config);
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
