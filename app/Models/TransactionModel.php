<?php

namespace App\Models;

class TransactionModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "transactions";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "invoice_number",
        "user_id",
        "name",
        "description",
        "price",
        "image",
        "snap_url",
        "date",
        "quantity",
        "gross_amount",
        "check_in",
        "check_out",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function getImageAttribute() {
        return env("APP_URL") . "/storage/tickets/" . $this->attributes["image"];
    }

    public function user() {
        return $this->belongsTo(UserModel::class, "user_id");
    }

    public function histories() {
        return $this->hasMany(TransactionHistoryModel::class, "transaction_id");
    }

    public function latestHistory() {
        return $this->hasOne(TransactionHistoryModel::class, "transaction_id")->orderByDesc("id");
    }
}
