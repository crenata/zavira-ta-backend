<?php

namespace App\Models;

class ComplaintHistoryModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "complaint_histories";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "complaint_id",
        "status",
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}
