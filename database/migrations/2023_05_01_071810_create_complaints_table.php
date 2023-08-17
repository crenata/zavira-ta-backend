<?php

use App\Models\CityModel;
use App\Models\ComplaintModel;
use App\Models\UserModel;
use App\Traits\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use MigrationTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create($this->getTable(new ComplaintModel()), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("city_id");
            $table->string("name");
            $table->string("subject");
            $table->longText("description");
            $table->date("date");
            $table->string("image");
            $table->string("video");
            $this->timestamps($table);
            $this->softDeletes($table);

            $table->foreign("user_id")->references("id")->on($this->getTable(new UserModel()))->onDelete("cascade");
            $table->foreign("city_id")->references("id")->on($this->getTable(new CityModel()))->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new ComplaintModel()));
    }
};
