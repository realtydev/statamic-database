<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Realtydev\StatamicDatabase\Database\Seeders\DefaultBlueprintSeeder;

class CreateBlueprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blueprints', function (Blueprint $table) {
            $table->id();
            $table->string('namespace')->nullable()->default(null);
            $table->string('handle');
            $table->json('data');
            $table->timestamps();
        });

        // create default blueprint (required for Statamic)
        $defaultBlueprint = (new DefaultBlueprintSeeder())->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blueprints');
    }
}
