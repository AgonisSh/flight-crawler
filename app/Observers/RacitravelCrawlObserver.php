<?php

namespace App\Observers;

use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Carbon\Carbon;

class RacitravelCrawlObserver extends CrawlObserver
{
    private array $flights = [];

    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null
    ): void {
        $html = (string) $response->getBody();
        $crawler = new DomCrawler($html);

        // Racitravel specific selectors
        $crawler->filter('.flight-item')->each(function (DomCrawler $node) {
            $this->flights[] = [
                'airline' => $node->filter('.airline-name')->text(''),
                'price' => $this->cleanPrice($node->filter('.price-amount')->text('')),
                'departure_time' => $node->filter('.departure-time')->text(''),
                'arrival_time' => $node->filter('.arrival-time')->text(''),
                'stops' => $node->filter('.stops')->text(''),
                'duration' => $node->filter('.duration')->text(''),
                'flight_number' => $node->filter('.flight-number')->text('')
            ];
        });
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null
    ): void {
        Log::error('Racitravel crawl failed', [
            'url' => $url->__toString(),
            'error' => $requestException->getMessage()
        ]);
    }

    private function cleanPrice(string $price): float
    {
        return (float) preg_replace('/[^0-9.]/', '', $price);
    }

    public function getFlights(): array
    {
        return $this->flights;
    }
}
