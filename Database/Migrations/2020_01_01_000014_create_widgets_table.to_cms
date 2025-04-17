<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
// ----- bases ----
use Modules\Xot\Database\Migrations\XotBaseMigration;

// ----- models -----

/**
 * Class CreateWidgetsTable.
 */
class CreateWidgetsTable extends XotBaseMigration
{
    /**
     * db up.
     *
     * @return void
     */
    public function up()
    {
        // -- CREATE --
        $this->tableCreate(
            function (Blueprint $table) {
                $table->increments('id');
                $table->nullableMorphs('post');
                $table->string('blade')->nullable();
                $table->string('image_src')->nullable();
                $table->integer('pos')->nullable();
                $table->string('model')->nullable();
                $table->integer('limit')->nullable();
                $table->string('order_by')->nullable();
                $table->timestamps();
            }
        ); // end create

        // -- UPDATE --
        $this->tableUpdate(
            function (Blueprint $table) {
                if (! $this->hasColumn('updated_at')) {
                    $table->timestamps();
                }
                if (! $this->hasColumn('updated_by')) {
                    $table->string('updated_by')->nullable()->after('updated_at');
                    $table->string('created_by')->nullable()->after('created_at');
                }
                if (! $this->hasColumn('title')) {
                    $table->string('title')->nullable()->after('post_type');
                }

                if (! $this->hasColumn('layout_position')) {
                    $table->string('layout_position')->nullable()->after('post_type');
                }
            }
        ); // end update
    }
}
