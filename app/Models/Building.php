<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// helpers
use App\Helpers\Helpers;

class Building extends Model {
    protected $table = 'mt_building';

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function getUniqueID()
    {
        $config = [
            'table' => $this->table,
            'prefix' => 'UNTB-',
            'length' => 5
        ];

        return Helpers::generateUniqueID($config);
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    // bank
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    // buildingStatus
    public function buildingStatus()
    {
        return $this->hasOne(BuildingStatus::class, 'building_id');
    }
}
