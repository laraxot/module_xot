<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//----- bases ----
use Modules\Xot\Database\Migrations\XotBaseMigration;
//----- models -----
use Modules\Xot\Models\Metatag as MyModel;

class CreateMetatagsTable extends XotBaseMigration {
    public function up() {
        //-- CREATE --
        if (! $this->tableExists()) {
            $this->getConn()->create($this->getTable(), function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->text('subtitle')->nullable();
                $table->string('charset')->nullable();
                $table->string('author')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->text('logo_src')->nullable();
                $table->text('logo_footer_src')->nullable();
                $table->string('tennant_name')->nullable();
            });
        }//end create

        //-- UPDATE --
        $this->getConn()->table($this->getTable(), function (Blueprint $table) {
            if (! $this->hasColumn('updated_at')) {
                $table->timestamps();
            }
            if (! $this->hasColumn('updated_by')) {
                $table->string('updated_by')->nullable()->after('updated_at');
                $table->string('created_by')->nullable()->after('created_at');
            }
        }); //end update
    }
}
