<?php

declare(strict_types=1);

namespace Modules\Xot\Providers\Traits;

use Modules\Cms\Contracts\PanelPresenterContract;
use Modules\Xot\Presenters\GeoJsonPanelPresenter;
use Modules\Xot\Presenters\HtmlPanelPresenter;
use Modules\Xot\Presenters\JsonPanelPresenter;

trait PresenterTrait
{
    private function registerPresenter(): void
    {
        $responseType = request()->input('responseType');
        $responses = [
            // 'html'=> HtmlPanelPresenter::class,//default
            'json' => JsonPanelPresenter::class,
            'geoJson' => GeoJsonPanelPresenter::class,
            // 'pdf'=>PdfPanelPresenter::class,
            // 'xls'=>XlsPanelPresenter::class,
        ];
        $response = HtmlPanelPresenter::class;
        if (isset($responses[$responseType])) {
            $response = $responses[$responseType];
        }

        $this->app->bind(
            PanelPresenterContract::class,
            // HtmlPanelPresenter::class,
            $response,
        );
    }
}
