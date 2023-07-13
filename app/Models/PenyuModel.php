<?php

namespace App\Models;

use Illuminate\Support\Carbon;

class PenyuModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "penyu";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "adopt",
        "menetas",
        "gagal_menetas",
        "belum_menetas",
        "year",
        "month",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    protected $appends = [
        "date"
    ];

    public function getDateAttribute() {
        return Carbon::createFromDate($this->attributes["year"], $this->attributes["month"])->format("F Y");
    }
}
