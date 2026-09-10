<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->string('birth_place')->nullable();
            $table->string('ration_agent_name')->nullable();
            $table->date('housing_card_date')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->string('current_governorate')->nullable();
            $table->string('current_district')->nullable();
            $table->string('current_subdistrict')->nullable();
            $table->string('current_neighborhood')->nullable();
            $table->string('current_alley')->nullable();
            $table->string('previous_governorate')->nullable();
            $table->string('previous_district')->nullable();
            $table->string('previous_subdistrict')->nullable();
            $table->string('previous_neighborhood')->nullable();
            $table->string('previous_landmark')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place',
                'ration_agent_name',
                'housing_card_date',
                'issuing_authority',
                'current_governorate',
                'current_district',
                'current_subdistrict',
                'current_neighborhood',
                'current_alley',
                'previous_governorate',
                'previous_district',
                'previous_subdistrict',
                'previous_neighborhood',
                'previous_landmark',
            ]);
        });
    }
};
