<?php

namespace App\Actions;

use App\Observers\FlightCrawlObserver;
use App\DTOs\FlightResultDTO;
use Symfony\Component\DomCrawler\Crawler;

class RaciTravelCrawlerAction extends FlightCrawlObserver
{
    protected function parseFlightData(string $html): void
    {
        $crawler = new Crawler($html);

        $crawler->filter('.flight-card')->each(function (Crawler $node) {
            $this->results->push(new FlightResultDTO(
                airline: $node->filter('.airline-name')->text(''),
                flightNumber: $node->filter('.flight-number')->text(''),
                price: $this->extractPrice($node->filter('.price')->text('')),
                departureTime: $node->filter('.departure-time')->text(''),
                arrivalTime: $node->filter('.arrival-time')->text(''),
                currency: 'EUR'
            ));
        });
    }

    private function extractPrice(string $price): float
    {
        return (float) preg_replace('/[^0-9.]/', '', $price);
    }
}
