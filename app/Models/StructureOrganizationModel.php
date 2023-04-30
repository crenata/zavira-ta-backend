<?php

namespace App\Models;

class StructureOrganizationModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "structure_organizations";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "image",
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    public function getImageAttribute() {
        return env("APP_URL") . "/storage/structure-organizations/" . $this->attributes["image"];
    }
}
