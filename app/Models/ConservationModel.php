<?php

namespace App\Models;

class ConservationModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "conservations";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "city_id",
        "name",
        "wide",
        "target",
        "penetapan",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function city() {
        return $this->belongsTo(CityModel::class, "city_id");
    }
}
