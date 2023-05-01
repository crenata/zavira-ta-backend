<?php

namespace App\Models;

class TicketModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "tickets";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "description",
        "price",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}
