<?php

namespace App\Services;
use App\Observers\RacitravelCrawlObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\Crawler\Crawler;
use GuzzleHttp\RequestOptions;

class RacitravelScraperService
{
    public function scrapePrices(
        string $origin,
        string $destination,
        string $departureDate,
        ?string $returnDate = null,
        int $adults = 1,
        int $children = 0,
        int $infants = 0
    ): array {
        $cacheKey = "racitravel_prices_{$origin}_{$destination}_{$departureDate}_{$returnDate}_{$adults}_{$children}_{$infants}";

        return Cache::remember($cacheKey, 3600, function () use (
            $origin,
            $destination,
            $departureDate,
            $returnDate,
            $adults,
            $children,
            $infants
        ) {
            try {
                return $this->crawlForPrices($origin, $destination, $departureDate, $returnDate, $adults, $children, $infants);
            } catch (\Exception $e) {
                Log::error('Racitravel scraping failed', [
                    'error' => $e->getMessage(),
                    'origin' => $origin,
                    'destination' => $destination,
                    'departure_date' => $departureDate
                ]);
                return [];
            }
        });
    }

    private function crawlForPrices(
        string $origin,
        string $destination,
        string $departureDate,
        ?string $returnDate,
        int $adults,
        int $children,
        int $infants
    ): array {
        $originCity = $this->cityCodes[strtoupper($origin)] ?? strtolower($origin);
        $destCity = $this->cityCodes[strtoupper($destination)] ?? strtolower($destination);

        // Format dates for Racitravel URL (YYYY-MM-DD)
        $depDate = Carbon::parse($departureDate)->format('Y-m-d');
        $retDate = $returnDate ? Carbon::parse($returnDate)->format('Y-m-d') : null;

        // Construct Racitravel URL
        $url = "https://www.racitravel.com/vuelos/{$originCity}/{$destCity}";
        $url .= "?departureDate={$depDate}";
        if ($returnDate) {
            $url .= "&returnDate={$retDate}";
        }
        $url .= "&adults={$adults}&children={$children}&infants={$infants}";

        $observer = new RacitravelCrawlObserver();

        Crawler::create([
            RequestOptions::VERIFY => false,
            RequestOptions::TIMEOUT => 30,
            RequestOptions::HEADERS => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
                'Sec-Fetch-Dest' => 'document',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-Site' => 'none',
                'Sec-Fetch-User' => '?1',
                'Cache-Control' => 'max-age=0'
            ]
        ])
            ->setConcurrency(1) // Respect rate limits
            ->setMaximumDepth(1)
            ->setDelayBetweenRequests(2000) // 2 seconds delay
            ->setCrawlObserver($observer)
            ->startCrawling($url);

        return $observer->getFlights();
    }
}

