<?php

namespace Modules\Xot\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use File;
//---- services --
use Modules\Theme\Services\ThemeService;
use Modules\Xot\Services\TranslatorService;

/**
 * Class TranslationController
 * @package Modules\Xot\Http\Controllers
 */
class TranslationController extends Controller {
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request) {
        return ThemeService::view();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        $data = $request->all();
        $trans = $data['trans'];
        TranslatorService::store($trans);
        if (\Request::ajax()) {
            $response = [
                'success' => true,
                //'data'    => $result,
                'message' => 'OK',
            ];
            $response = \array_merge($data, $response);

            return response()->json($response, 200);
        }

        return redirect()->back();
    }
}
