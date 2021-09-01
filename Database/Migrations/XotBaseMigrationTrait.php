<?php

declare(strict_types=1);

namespace Modules\Xot\Database\Migrations;

/**
 * Trait XotBaseMigrationTrait.
 */
trait XotBaseMigrationTrait {
    public function getTable(): string {
        return with(new MyModel())->getTable();
    }

    /**
     * @return mixed
     */
    public function getConn() {
        $conn_name = with(new MyModel())->getConnectionName();
        $conn = Schema::connection($conn_name);

        return $conn;
    }

    /**
     * @return mixed
     */
    public function tableExists() {
        return $this->getConn()->hasTable($this->getTable());
    }

    /**
     * @param $col
     *
     * @return mixed
     */
    public function hasColumn($col) {
        return $this->getConn()->hasColumn($this->getTable(), $col);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @return void
     */
    public function down() {
        $this->getConn()->dropIfExists($this->getTable());
    }
}
