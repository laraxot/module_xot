<?php

namespace Modules\Xot\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Modules\Extend\Traits\CrudContainerItemNoPostTrait as CrudTrait;

abstract class XotBaseController extends Controller{

	use CrudTrait;
	/*
	public function index(Request $request,$model){

	}
	*/

}