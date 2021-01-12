<?php

declare(strict_types=1);

namespace Modules\Xot\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Xot\Services\PanelRouteService;

//use Illuminate\Support\Facades\Request;

//use Illuminate\Contracts\Auth\UserProvider;

/**
 * Modules\Xot\Contracts\PanelContract.
 *
 * @property ModelContract|null         $row
 * @property bool                       $in_admin
 * @property PanelRouteService          $route
 * @property Collection|ModelContract[] $rows
 *
 * @method mixed                          findParentType($type)
 * @method string                         imgSrc($params)
 * @method \Illuminate\Support\Collection getParents()
 * @method string                         postType()
 * @method string                         guid()
 * @method string                         pdfFilename()
 * @method array                          with()
 * @method mixed                          pdf($params)
 * @method PanelContract|null             getParent()
 * @method array                          fields()
 * @method array                          actions()
 * @method Builder                        rows()
 * @method string                         getModuleNameLow()
 * @method bool                           isSuperAdmin()
 * @method mixed                          itemAction()
 * @method string|string[]|void           relatedUrl()
 * @method string                         url($params)
 * @method self                           setRow($row)
 * @method self                           update($data)
 * @method mixed                          out($data=null)
 * @method mixed                          callItemActionWithGate($act)
 * @method mixed                          rules($params)
 * @method array                          rulesMessages()
 * @method array                          areas()
 */
interface PanelContract {
    /**
     * @param ModelContract|null $rows
     *
     * @return iterable|null
     */
    public function setRows($rows);

    /**
     * @return object|null
     */
    public function setItem(string $guid);

    public function setParent(PanelContract $panel): PanelContract;

    public function view($params = null);

    public function itemAction($act);

    public function relatedUrl($params);

    public function setLabel(string $label): ModelContract;

    /*
    public function __construct($model = null);

    public function setRow($row);



    public function initRows();

    public function setParent($parent);

    public function getParents();

    public function findParentType($type);

    public function optionId(object $row);

    public function optionLabel(object $row):string;



    public function hasLang();

    public function setLabel($label);

    public function optionsSelect();

    public function options($data = null);

    public function optionsTree($data = null);

    public function optionIdName();

    public function optionLabelName();

    public function search();

    public function orderBy();

    public function rules($params = []);

    public function pivotRules($params);

    public function rulesMessages();

    public function filters(Request $request = null);

    public function getXotModelName();

    public function indexNav();

    public function getActions($params = []);

    public function containerActions($params = []);

    public function itemActions($params = []);

    public function itemAction($act);

    public function containerAction($act);

    public function urlContainerAction($act);

    public function urlItemAction($act);

    public function btnItemAction($act);

    public static function indexQuery($data, $query);

    public static function relatableQuery(Request $request, $query);

    public function applyJoin($query);

    public function applyFilter($query, $filters);

    public function applySearch($query, $q);

    public function applySort($query, $sort);

    public function formatItemData($item, $params);

    public function formatData($data, $params);

    public function indexRows(Request $request, $query);

    public function formCreate($params = []);

    public function formEdit($params = []);

    public function exceptFields($params = []);

    public function indexFields();

    public function createFields();

    public function editFields();

    public function indexEditFields();

    public function addQuerystringsUrl($params);

    public function yearNavRedirect();

    public function yearNav();

    public function monthYearNav();

    public function btnSubmit();

    public function btnDelete($params = []);

    public function btnDetach($params = []);

    public function btnCrud($params = []);

    public function btnHtml($params);

    public function btn($act, $params = []);

    public function imageHtml($params);

    public function imgSrc($params);

    public function microdataSchemaOrg();

    public function show_ldJson();

    public function langUrl($lang);

    public function relatedUrlRecursive($params);

    public function relatedUrl($params);

    public function relatedName($name, $id = null);

    public function url($params = []);

    public function indexUrl();

    public function indexEditUrl();

    public function editUrl();

    public function updateUrl();

    public function showUrl();

    public function createUrl();

    public function storeUrl();

    public function destroyUrl();

    public function detachUrl();

    public function gearUrl();

    public function postType();

    public function guid();

    public function getItemTabs();

    public function getRowTabs();

    public function getTabs();

    public function rows($data = null);

    public function callItemActionWithGate($act);

    public function callAction($act);

    public function callItemAction($act);

    public function callContainerAction($act);

    public function out($params = []);

    public function pdfFilename($params = []);

    public function pdf($params = []);

    public static function getInstance();

    public function related($relationship);

    public function getModuleName();

    public function getModuleNameLow();

    public function breadcrumbs();

    public function getExcerpt($length = 225);

    public function indexEditSubs();

    public function swiperItem();

    public function dataTable();



    public function id();
    */
}
