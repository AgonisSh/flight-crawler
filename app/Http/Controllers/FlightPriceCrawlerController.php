<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightSearchRequest;
use App\Services\RacitravelScraperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Crawler\Crawler;

class FlightPriceCrawlerController extends Controller
{
    private RacitravelScraperService $scraperService;

    public function __construct(RacitravelScraperService $scraperService)
    {
        $this->scraperService = $scraperService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        /*$request->validate([
            'origin' => 'required|string|size:3',
            'destination' => 'required|string|size:3',
            'departure_date' => 'required|date|after:today',
            'return_date' => 'nullable|date|after:departure_date',
            'adults' => 'integer|min:1|max:9|required',
            'children' => 'integer|min:0|max:9|required',
            'infants' => 'integer|min:0|max:9|required',
        ]);*/

        $prices = $this->scraperService->scrapePrices(
            $request->input('origin'),
            $request->input('destination'),
            $request->input('departure_date'),
            $request->input('return_date'),
            $request->input('adults', 1),
            $request->input('children', 0),
            $request->input('infants', 0)
        );

        return response()->json([
            'success' => true,
            'data' => $prices
        ]);
    }
}
