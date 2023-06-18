<?php

namespace App\Models;

class GisModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "gis";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "wide",
        "target",
        "description",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}
