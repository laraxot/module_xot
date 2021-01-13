<?php

use Illuminate\Database\Schema\Blueprint;
//----- bases ----
use Modules\Xot\Database\Migrations\XotBaseMigration;

//----- models -----

/**
 * Class CreateConfsTable
 */
class CreateConfsTable extends XotBaseMigration {
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
                    $table->string('note')->nullable();
                    $table->timestamps();
                    $table->string('updated_by')->nullable();
                    $table->string('created_by')->nullable();
                }
            );
        }//end create

        //-- UPDATE --
        $this->getConn()->table(
            $this->getTable(),
            function (Blueprint $table) {
            }
        ); //end update
    }
}
