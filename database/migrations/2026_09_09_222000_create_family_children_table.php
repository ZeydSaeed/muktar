<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained('families')->cascadeOnDelete();
            $table->string('name'); // اسم الابن
            $table->date('birth_date')->nullable(); // التولد
            $table->string('national_id_number')->nullable(); // رقم هوية البطاقة الوطنية
            $table->unsignedInteger('registry_number')->nullable(); // السجل
            $table->unsignedInteger('page_number')->nullable(); // الصحيفة
            $table->string('profession')->nullable(); // المهنة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_children');
    }
};
