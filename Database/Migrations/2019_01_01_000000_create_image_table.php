<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//----- models -----
use Modules\Xot\Models\Image as MyModel;

class CreateImageTable extends Migration {
    public function getTable() {
        return with(new MyModel())->getTable();
    }

    public function up() {
        //----- create -----
        if (! Schema::hasTable($this->getTable())) {
            Schema::create($this->getTable(), function (Blueprint $table) {
                $table->increments('id');
                $table->nullableMorphs('post');
                $table->string('src');

                $table->integer('auth_user_id')->nullable()->index();
                $table->string('note')->nullable();

                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
                $table->timestamps();
            });
        }
        //----- update -----
        Schema::table($this->getTable(), function (Blueprint $table) {
        });
    }

    public function down() {
        Schema::dropIfExists($this->getTable());
    }
}
