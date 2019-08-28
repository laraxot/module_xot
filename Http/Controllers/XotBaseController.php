<?php

namespace Modules\Xot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Modules\Xot\Traits\CrudContainerItemNoPostTrait as CrudTrait;
//use Modules\Xot\Traits\CrudContainerItemRepositoryTrait as CrudTrait;


abstract class XotBaseController extends Controller{

	use CrudTrait;
	/*
	public function index(Request $request,$model){

	}
	*/

}