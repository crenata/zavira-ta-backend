<?php

namespace App\Models;

class ComplaintModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "complaints";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "subject",
        "description",
        "location",
        "date",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}
