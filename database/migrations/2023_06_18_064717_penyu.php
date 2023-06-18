<?php

use App\Models\PenyuModel;
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
        Schema::create($this->getTable(new PenyuModel()), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("menetas")->nullable();
            $table->unsignedBigInteger("gagal_menetas")->nullable();
            $table->unsignedBigInteger("jumlah_telur")->nullable();
            $table->date("date")->unique();
            $this->timestamps($table);
            $this->softDeletes($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new PenyuModel()));
    }
};
