<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Ensure Lookup Types exist
        $master = \App\Domain\Shared\Models\LookupMaster::firstOrCreate(
            ['code' => 'ACCOUNT_TYPE'],
            ['name' => 'نوع الحساب', 'description' => 'Type of the account']
        );

        $types = [
            ['code' => 'ADMIN', 'name' => 'مسؤول'],
            ['code' => 'STUDIO', 'name' => 'استوديو'],
            ['code' => 'SCHOOL', 'name' => 'مدرسة'],
            ['code' => 'SUBSCRIBER', 'name' => 'مشترك'],
        ];

        foreach ($types as $type) {
            $master->values()->firstOrCreate(['code' => $type['code']], $type);
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('account_type_id')->nullable()->after('account_status_id');
            $table->foreign('account_type_id')->references('lookup_value_id')->on('lookup_values');
            $table->index('account_type_id');
        });
    }

    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['account_type_id']);
            $table->dropColumn('account_type_id');
        });
        
        // Optional: We might not want to delete the lookup master/values on rollback 
        // to preserve data integrity if other things start using it, 
        // but for a strict rollback:
        // $master = \App\Domain\Shared\Models\LookupMaster::where('code', 'ACCOUNT_TYPE')->first();
        // if($master) {
        //     $master->values()->delete();
        //     $master->delete();
        // }
    }
};
