<?php
namespace app\models;

require_once 'User.php';

class Client
{
    private User $user;
    private string $name;
    private string $birthday;
    private float $rate;

    public function __construct(User $user, string $name, string $birthday, float $rate = 5.0)
    {
        $this->user = $user;
        $this->name = $name;
        $this->birthday = $birthday;
        $this->rate = $rate;
    }

    public function getId(): ?int
    {
        return $this->user->getId();
    }

    public function getPhone(): string
    {
        return $this->user->getPhone();
    }

    public function getPassword(): string
    {
        return $this->user->getPassword();
    }

    public function getRole(): string
    {
        return $this->user->getRole();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBirthday(): string
    {
        return $this->birthday;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }
}

