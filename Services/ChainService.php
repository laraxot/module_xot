<?php
namespace Modules\Xot\Services;

/**
* https://github.com/Tinyportal/TinyPortal/blob/master/Sources/TPSubs.php
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

	var $table;
   	var $rows;
   	var $chain_table;
   	var $primary_field;
   	var $parent_field;
   	var $sort_field;

   	function __construct($primary_field, $parent_field, $sort_field, $rows, $root_id, $maxlevel){
       	$this->rows = $rows;
       	$this->primary_field = $primary_field;
       	$this->parent_field = $parent_field;
       	$this->sort_field = $sort_field;
       	$this->buildChain($root_id,$maxlevel);
   	}

   	function buildChain($rootcatid,$maxlevel){
       	foreach($this->rows as $row){
           $this->table[$row[$this->parent_field]][ $row[$this->primary_field]] = $row;
       	}
       	$this->makeBranch($rootcatid, 0, $maxlevel);
   	}

   	function makeBranch($parent_id, $level, $maxlevel){
       	if(!is_array($this->table))   $this->table = array();
       	if(!array_key_exists($parent_id, $this->table))   return;
       	$rows = $this->table[$parent_id];
       	foreach($rows as $key=>$value){
           	$rows[$key]['key'] = $this->sort_field;
       	}
       	usort($rows, 'chainCMP');
       	foreach($rows as $item){
           	$item['indent'] = $level;
           	$this->chain_table[] = $item;
           	if((isset($this->table[$item[$this->primary_field]])) && (($maxlevel > $level + 1) || ($maxlevel == 0))){
               	$this->makeBranch($item[$this->primary_field], $level + 1, $maxlevel);
           	}
       	}
   	}
}

function chainCMP($a, $b){
   	if($a[$a['key']] == $b[$b['key']]){
    	return 0;
   	}
   	return($a[$a['key']] < $b[$b['key']]) ? -1 : 1;
}