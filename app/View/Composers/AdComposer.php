<?php

namespace App\View\Composers;

use App\Services\AdService;
use Illuminate\View\View;
use Illuminate\Http\Request;

class AdComposer
{
    protected $adService;
    protected $request;

    public function __construct(AdService $adService, Request $request)
    {
        $this->adService = $adService;
        $this->request = $request;
    }

    public function compose(View $view)
    {
        $placements = [
            'header' => $this->adService->getAdsForPlacement('header', $this->request, 1),
            'sidebar' => $this->adService->getAdsForPlacement('sidebar', $this->request, 2),
            'footer' => $this->adService->getAdsForPlacement('footer', $this->request, 1),
            'in-content' => $this->adService->getAdsForPlacement('in-content', $this->request, 1),
            'floating' => $this->adService->getAdsForPlacement('floating', $this->request, 1),
            'popup' => $this->adService->getAdsForPlacement('popup', $this->request, 1),
            'interstitial' => $this->adService->getAdsForPlacement('interstitial', $this->request, 1),
        ];

        $view->with('adPlacements', $placements);
    }
}