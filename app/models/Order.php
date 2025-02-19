<?php

namespace app\models;

use DateTime;
use JsonSerializable;

class Order implements JsonSerializable
{
    private int $id;
    private float $price;
    private DateTime $date;
    private bool $baby;
    private Car $car;
    private Driver $driver;
    private Client $client;

    public function __construct(
        int $id,
        float $price,
        string $date,
        bool $baby,
        Car $car,
        Driver $driver,
        Client $client
    ) {
        $this->id = $id;
        $this->price = $price;
        $this->date = new DateTime($date);
        $this->baby = $baby;
        $this->car = $car;
        $this->driver = $driver;
        $this->client = $client;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'date' => $this->date,
            'baby' => $this->baby,
            'car' => $this->car,
            'driver' => $this->driver,
            'client' => $this->client,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDate(): string
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    public function getBaby(): bool
    {
        return $this->baby;
    }

    public function getCar(): Car
    {
        return $this->car;
    }

    public function getDriver(): Driver
    {
        return $this->driver;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setDate(string $date): void
    {
        $this->date = new DateTime($date);
    }

    public function setBaby(bool $baby): void
    {
        $this->baby = $baby;
    }

    public function setCar(Car $car): void
    {
        $this->car = $car;
    }

    public function setDriver(Driver $driver): void
    {
        $this->driver = $driver;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
