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
        Schema::create('contract_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('content');
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_template_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('contractable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('contract_templates');
    }
};
