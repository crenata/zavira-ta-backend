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
        "city_id",
        "name",
        "subject",
        "description",
        "date",
        "image",
        "video",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function getImageAttribute() {
        return env("APP_URL") . "/storage/complaints/" . $this->attributes["image"];
    }

    public function getVideoAttribute() {
        return env("APP_URL") . "/storage/complaints/" . $this->attributes["video"];
    }

    public function user() {
        return $this->belongsTo(UserModel::class, "user_id");
    }

    public function city() {
        return $this->belongsTo(CityModel::class, "city_id");
    }

    public function histories() {
        return $this->hasMany(ComplaintHistoryModel::class, "complaint_id")->orderByDesc("id");
    }

    public function latestHistory() {
        return $this->hasOne(ComplaintHistoryModel::class, "complaint_id")->orderByDesc("id");
    }
}
