<?php

use App\Models\CityModel;
use App\Models\TerumbuModel;
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
        Schema::create($this->getTable(new TerumbuModel()), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("city_id");
            $table->string("name");
            $table->string("zone");
            $table->unsignedBigInteger("percentage");
            $table->year("year");
            $this->timestamps($table);
            $this->softDeletes($table);

            $table->foreign("city_id")->references("id")->on($this->getTable(new CityModel()))->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new TerumbuModel()));
    }
};
