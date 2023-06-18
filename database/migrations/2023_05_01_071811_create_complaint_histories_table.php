<?php

use App\Models\AdminModel;
use App\Models\ComplaintHistoryModel;
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
        Schema::create($this->getTable(new ComplaintHistoryModel()), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("complaint_id");
            $table->unsignedBigInteger("assignee_id")->nullable();
            $table->unsignedBigInteger("modifier_id")->nullable();
            $table->unsignedBigInteger("status");
            $table->unsignedBigInteger("tracking_type")->nullable();
            $this->timestamps($table);
            $this->softDeletes($table);

            $table->foreign("complaint_id")->references("id")->on($this->getTable(new ComplaintModel()))->onDelete("cascade");
            $table->foreign("assignee_id")->references("id")->on($this->getTable(new AdminModel()))->onDelete("cascade");
            $table->foreign("modifier_id")->references("id")->on($this->getTable(new AdminModel()))->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists($this->getTable(new ComplaintHistoryModel()));
    }
};
