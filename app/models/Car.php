<?php

namespace app\models;

use JsonSerializable;

class Car implements JsonSerializable
{
    private int $id;
    private string $number;
    private string $release;
    private bool $baby_seat;

    public function __construct(
        int $id,
        string $number,
        string $release,
        bool $baby_seat = true
    ) {
        $this->id = $id;
        $this->number = $number;
        $this->release = $release;
        $this->baby_seat = $baby_seat;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'release' => $this->release,
            'baby_seat' => $this->baby_seat
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getRelease(): string
    {
        return $this->release;
    }

    public function getBabySeat(): bool
    {
        return $this->baby_seat;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function setRelease(int $release): void
    {
        $this->release = $release;
    }

    public function setBabySeat(bool $baby_seat): void
    {
        $this->baby_seat = $baby_seat;
    }
}
