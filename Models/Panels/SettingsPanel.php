<?php
namespace Modules\Xot\Models\Panels;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

//--- Services --
use Modules\Xot\Services\StubService;
use Modules\Xot\Services\RouteService;


use Modules\Xot\Models\Panels\XotBasePanel;

class SettingsPanel extends XotBasePanel {
	/**
	 * The model the resource corresponds to.
	 *
	 * @var string
	 */
	public static $model = 'Modules\Xot\Models\Settings';

	/**
	 * The single value that should be used to represent the resource when being displayed.
	 *
	 * @var string
	 */
	public static $title = "title";

	/**
	 * The columns that should be searched.
	 *
	 * @var array
	 */
	public static $search = array (
) ;

	/**
	* The relationships that should be eager loaded on index queries.
	*
	* @var array
	*/
	public function with(){
	  return [];
	}

	public function search(){
		return [];
	}

	/**
	 * on select the option id
	 *
	 */

	public function optionId($row){
		return $row->area_id;
	}

	/**
	 * on select the option label
	 *
	 */

	public function optionLabel($row){
		return $row->area_define_name;
	}





	public function fields(){
		return array (

  (object) array(
     'type' => 'Id',
     'name' => 'id',
     'comment' => NULL,
  ),
  (object) array(
     'type' => 'String',
     'name' => 'appname',
     'rules' => 'required',
     'comment' => NULL,
  ),
  (object) array(
     'type' => 'String',
     'name' => 'description',
     'rules' => 'required',
     'comment' => NULL,
  ),
  (object) array(
     'type' => 'Text',
     'name' => 'keywords',
     'comment' => 'not in Doctrine',
  ),
  (object) array(
     'type' => 'Text',
     'name' => 'author',
     'comment' => 'not in Doctrine',
  ),
);
	}

	/**
	 * Get the tabs available
	 *
	 * @return array
	 */
	public function tabs(){
		$tabs_name = [];
		return $tabs_name;
	}
	/**
	 * Get the cards available for the request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function cards(Request $request){
		return [];
	}

	/**
	 * Get the filters available for the resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function filters(Request $request=null){
		return [];
	}

	/**
	 * Get the lenses available for the resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function lenses(Request $request){
		return [];
	}

	/**
	 * Get the actions available for the resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function actions(Request $request=null){
		return [];
	}

}
