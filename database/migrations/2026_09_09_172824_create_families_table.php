<?php

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
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->string('head_of_family_name'); // اسم رب الاسرة
            $table->string('spouse_name')->nullable(); // اسم الزوجة او الزوج
            $table->string('profession')->nullable(); // المهنة
            $table->string('mother_name')->nullable(); // اسم الام
            $table->unsignedInteger('house_number')->nullable(); // رقم الدار
            $table->unsignedBigInteger('ration_card_number')->nullable(); // رقم البطاقة التموينية
            $table->unsignedBigInteger('housing_card_number')->nullable(); // رقم بطاقة السكن
            $table->string('national_id_number')->nullable(); // رقم البطاقة الوطنية
            $table->string('mobile_number')->nullable(); // رقم الموبايل
            $table->date('current_residence_date')->nullable(); // تاريخ السكن الحالي
            $table->date('birth_date')->nullable(); // المواليد
            $table->unsignedInteger('family_members_count')->nullable(); // عدد افراد الاسرة
            $table->unsignedInteger('males_count')->nullable(); // عدد الذكور
            $table->unsignedInteger('females_count')->nullable(); // عدد الاناث
            $table->string('previous_address')->nullable(); // عنوان السكن السابق
            $table->string('previous_mukhtar_name')->nullable(); // اسم المختار السابق
            $table->date('karbala_residence_date')->nullable(); // تاريخ السكن في كربلاء
            $table->unsignedInteger('registry_page_number')->nullable(); // رقم الصفحة في السجل
            $table->string('nearest_landmark')->nullable(); // اقرب نقطة دالة
            $table->string('secondary_mobile_number')->nullable(); // رقم موبايل ثاني
            $table->text('notes')->nullable(); // الملاحظات
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
