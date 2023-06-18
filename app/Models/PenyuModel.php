<?php

namespace App\Models;

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
        "menetas",
        "gagal_menetas",
        "jumlah_telur",
        "date",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}
