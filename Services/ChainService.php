<?php

namespace Modules\Xot\Services;

/**
 * https://github.com/Tinyportal/TinyPortal/blob/master/Sources/TPSubs.php.
 *
 **/

/* example to use
$ordered = chain('category_id', 'parent_id', 'category_position', $rows);

foreach($ordered as $item){
    echo str_repeat('------', $item['indent']).$item['category_name'].'<br>';
}

*/

function chain($primary_field, $parent_field, $sort_field, $rows, $root_id = 0, $maxlevel = 25) {
    $c = new ChainService($primary_field, $parent_field, $sort_field, $rows, $root_id, $maxlevel);

    return $c->chain_table;
}

class ChainService {
    public $table;
    public $rows;
    public $chain_table;
    public $primary_field;
    public $parent_field;
    public $sort_field;

    public function __construct($primary_field, $parent_field, $sort_field, $rows, $root_id = 0, $maxlevel = 25) {
        $this->rows = $rows;
        $this->primary_field = $primary_field;
        $this->parent_field = $parent_field;
        $this->sort_field = $sort_field;
        $this->buildChain($root_id, $maxlevel);
    }

    public function buildChain($rootcatid, $maxlevel) {
        foreach ($this->rows as $row) {
            $this->table[$row[$this->parent_field]][$row[$this->primary_field]] = $row;
        }
        $this->makeBranch($rootcatid, 0, $maxlevel);
    }

    public function makeBranch($parent_id, $level, $maxlevel) {
        if (! is_array($this->table)) {
            $this->table = [];
        }
        if (! array_key_exists($parent_id, $this->table)) {
            return;
        }
        $rows = $this->table[$parent_id];
        foreach ($rows as $key => $value) {
            $rows[$key]['key'] = $this->sort_field;
        }
        usort($rows, [$this, 'chainCMP']);
        foreach ($rows as $item) {
            $item['indent'] = $level;
            $this->chain_table[] = $item;
            if ((isset($this->table[$item[$this->primary_field]])) && (($maxlevel > $level + 1) || (0 == $maxlevel))) {
                $this->makeBranch($item[$this->primary_field], $level + 1, $maxlevel);
            }
        }
    }

    public function chainCMP($a, $b) {
        if ($a[$a['key']] == $b[$b['key']]) {
            return 0;
        }

        return($a[$a['key']] < $b[$b['key']]) ? -1 : 1;
    }
}
/*
function chainCMP($a, $b){
    if($a[$a['key']] == $b[$b['key']]){
        return 0;
    }
    return($a[$a['key']] < $b[$b['key']]) ? -1 : 1;
}
*/
