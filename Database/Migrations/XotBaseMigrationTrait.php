<?php

namespace Modules\Xot\Database\Migrations;

trait XotBaseMigrationTrait {
    public function getTable() {
        return with(new MyModel())->getTable();
    }

    public function getConn() {
        $conn_name = with(new MyModel())->getConnectionName();
        $conn = Schema::connection($conn_name);

        return $conn;
    }

    public function tableExists() {
        return $this->getConn()->hasTable($this->getTable());
    }

    public function hasColumn($col) {
        return $this->getConn()->hasColumn($this->getTable(), $col);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $this->getConn()->dropIfExists($this->getTable());
    }
}
