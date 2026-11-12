
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('id_business_profile')
                ->nullable()
                ->unique()
                ->after('user_id');

            $table->text('offering_description')->nullable();
            $table->text('seeking_description')->nullable();

            $table->foreign('id_business_profile')
                ->references('id_business_profile')
                ->on('business_profiles')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['id_business_profile']);
            $table->dropUnique(['id_business_profile']);
            $table->dropColumn([
                'id_business_profile',
                'offering_description',
                'seeking_description',
            ]);
        });
    }
};
