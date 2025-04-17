<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * https://github.com/Tinyportal/TinyPortal/blob/master/Sources/TPSubs.php.
 */

/* example to use
$ordered = chain('category_id', 'parent_id', 'category_position', $rows);

foreach($ordered as $item){
    echo str_repeat('------', $item['indent']).$item['category_name'].'<br>';
}

*/

function chain(string $primary_field, string $parent_field, string $sort_field, Collection $rows, int $root_id = 0, int $maxlevel = 25): array
{
    $c = new ChainService($primary_field, $parent_field, $sort_field, $rows, $root_id, $maxlevel);

    return $c->chain_table;
}

/**
 * Class ChainService.
 */
class ChainService
{
    public array $table;

    /**
     * Undocumented variable.
     *
     * @var Collection<Model>
     */
    public Collection $rows;

    public array $chain_table;

    public string $primary_field;

    public string $parent_field;

    public string $sort_field;

    /**
     * ChainService constructor.
     *
     * @return void
     */
    public function __construct(string $primary_field, string $parent_field, string $sort_field, Collection $rows, int $root_id = 0, int $maxlevel = 25)
    {
        $this->rows = $rows;
        $this->primary_field = $primary_field;
        $this->parent_field = $parent_field;
        $this->sort_field = $sort_field;
        $this->chain_table = [];
        $this->table = [];

        $this->buildChain($root_id, $maxlevel);
    }

    /**
     * @param int $rootcatid
     * @param int $maxlevel
     */
    public function buildChain($rootcatid, $maxlevel): void
    {
        foreach ($this->rows as $row) {
            // considerando che ChainService viene utilizzato da XotBasePanel->optionsTree()
            // che a sua volta viene utilizzato in FormX\Resources\views\collective\fields\select\field_parent.blade.php
            // che vuole parent_id (radice) uguale a 0
            // controllo che la row radice non abbia parent_id uguale a null, in caso...
            if (null === $row[$this->parent_field]) {
                $row[$this->parent_field] = 0;
                $row->save();
            }
            $this->table[$row[$this->parent_field]][$row[$this->primary_field]] = $row;
        }
        $this->makeBranch($rootcatid, 0, $maxlevel);
    }

    /**
     * @param int $parent_id
     * @param int $level
     * @param int $maxlevel
     */
    public function makeBranch($parent_id, $level, $maxlevel): void
    {
        if (! \is_array($this->table)) {
            $this->table = [];
        }
        // dddx([$this->table, $parent_id]);
        if (! \array_key_exists($parent_id, $this->table)) {
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
            if ((isset($this->table[$item[$this->primary_field]])) && (($maxlevel > $level + 1) || (0 === $maxlevel))) {
                $this->makeBranch($item[$this->primary_field], $level + 1, $maxlevel);
            }
        }
    }

    /**
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    public function chainCMP($a, $b)
    {
        if ($a[$a['key']] === $b[$b['key']]) {
            return 0;
        }

        return ($a[$a['key']] < $b[$b['key']]) ? -1 : 1;
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
