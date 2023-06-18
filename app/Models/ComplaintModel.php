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
        "user_id",
        "name",
        "subject",
        "description",
        "location",
        "date",
        "image",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function getImageAttribute() {
        return env("APP_URL") . "/storage/complaints/" . $this->attributes["image"];
    }

    public function user() {
        return $this->belongsTo(UserModel::class, "user_id");
    }

    public function histories() {
        return $this->hasMany(ComplaintHistoryModel::class, "complaint_id");
    }

    public function latestHistory() {
        return $this->hasOne(ComplaintHistoryModel::class, "complaint_id")->orderByDesc("id");
    }
}
