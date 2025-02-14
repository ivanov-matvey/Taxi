<?php
namespace Models;

class User
{
    protected ?int $id;
    protected string $phone;
    protected string $password;
    protected string $role;
    protected bool $is_authenticated;

    public function __construct(?int $id, string $phone, string $password, string $role, bool $is_authenticated)
    {
        $this->id = $id;
        $this->phone = $phone;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->role = $role;
        $this->is_authenticated = $is_authenticated;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getIsAuthenticated(): ?int
    {
        return $this->is_authenticated;
    }
}
