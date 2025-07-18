<?php

use App\Models\Task\TaskStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('description');
            $table->unsignedBigInteger('ownerId');
            $table->unsignedBigInteger('assigneeId');
            $table->dateTime('endTask');
            $table->timestamp('finished_at')->nullable();
            $table->enum('status', [TaskStatusEnum::NEW->value, TaskStatusEnum::ACTIVE->value, TaskStatusEnum::COMPLETED->value])->default(TaskStatusEnum::NEW->value);
            $table->timestamps();

            $table->foreign('ownerId')->references('id')->on('users');
            $table->foreign('assigneeId')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
