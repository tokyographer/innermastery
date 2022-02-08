<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgentIdUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'agent_id')) {
                $table->integer('agent_id')->nullable();
            }
        });

        Schema::table('core_roles', function (Blueprint $table) {
            if (!Schema::hasColumn('core_roles', 'only_client')) {
                $table->tinyInteger('only_client')->after('updated_at')->default(0);
            }
        });

        Schema::table('bravo_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bravo_bookings', 'agent_id')) {
                $table->integer('agent_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
