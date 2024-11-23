<?php

namespace App\DTOs;

class FlightResultDTO
{
    public function __construct(
        public readonly string $airline,
        public readonly string $flightNumber,
        public readonly float $price,
        public readonly string $departureTime,
        public readonly string $arrivalTime,
        public readonly string $currency,
        public readonly ?string $duration = null,
    ) {}

    public function toArray(): array
    {
        return [
            'airline' => $this->airline,
            'flight_number' => $this->flightNumber,
            'price' => $this->price,
            'departure_time' => $this->departureTime,
            'arrival_time' => $this->arrivalTime,
            'currency' => $this->currency,
            'duration' => $this->duration,
        ];
    }
}
