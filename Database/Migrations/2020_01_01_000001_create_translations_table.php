<?php

use Illuminate\Database\Schema\Blueprint;
//--- models --
///use Modules\Blog\Models\Post as MyModel;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/**
 * Class CreateTranslationsTable
 */
class CreateTranslationsTable extends XotBaseMigration {
/**
* db up
*
* @return void
*/
    public function up() {
        //-- CREATE --
        if (! $this->tableExists()) {
            $this->getConn()->create(
                $this->getTable(),
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('lang')->nullable();
                    $table->string('key')->nullable();
                    $table->text('value')->nullable();
                    $table->string('created_by')->nullable();
                    $table->string('updated_by')->nullable();

                    $table->timestamps();
                }
            );
        }
        //-- UPDATE --
        $this->getConn()->table(
            $this->getTable(),
            function (Blueprint $table) {
                if (! $this->hasColumn('created_by')) {
                    $table->string('created_by')->nullable();
                }
                if (! $this->hasColumn('updated_by')) {
                    $table->string('updated_by')->nullable();
                }
            }
        );
    }

    //end up

    //end down
}//end class
