<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCacheTable extends Migration {
    public function getTable() {
        return 'cache';
    }

    /**
     * Run the migrations.
     */
    public function up() {
        if (! Schema::hasTable($this->getTable())) {
            Schema::create($this->getTable(), function (Blueprint $table) {
                $table->string('key', 64)->unique();
                $table->text('value');
                $table->integer('expiration');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists($this->getTable());
    }
}
