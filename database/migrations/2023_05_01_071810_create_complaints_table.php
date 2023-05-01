<?php

use App\Models\ComplaintModel;
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
            $table->string("name");
            $table->string("subject");
            $table->longText("description");
            $table->longText("location");
            $table->date("date");
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
        Schema::dropIfExists($this->getTable(new ComplaintModel()));
    }
};
