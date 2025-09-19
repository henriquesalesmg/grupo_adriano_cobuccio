<?php

namespace App\DTOs;

use Illuminate\Support\Facades\Hash;

class UserUpdateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?string $cpf = null,
        public readonly ?string $birthdate = null,
        public readonly ?string $securityAnswer = null,
        public readonly ?int $id = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            cpf: $data['cpf'] ?? null,
            birthdate: $data['birthdate'] ?? null,
            securityAnswer: $data['security_answer'] ?? null
        );
    }

    public static function forUpdate(array $data, ?int $userId = null): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'] ?? null,
            password: $data['password'] ?? null,
            cpf: $data['cpf'] ?? null,
            birthdate: $data['birthdate'] ?? null,
            securityAnswer: $data['security_answer'] ?? null,
            id: $userId
        );
    }

    public function getUpdateData(): array
    {
        $data = [
            'name' => $this->name,
        ];

        if ($this->email) {
            $data['email'] = $this->email;
        }

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->cpf) {
            $data['cpf'] = $this->cpf;
        }

        if ($this->birthdate) {
            $data['birthdate'] = $this->birthdate;
        }

        if ($this->securityAnswer) {
            $data['security_answer'] = $this->securityAnswer;
        }

        return $data;
    }

    public function getCreationData(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password ? Hash::make($this->password) : null,
            'cpf' => $this->cpf,
            'birthdate' => $this->birthdate,
            'security_answer' => $this->securityAnswer,
        ];
    }

    public function hasPasswordChange(): bool
    {
        return !empty($this->password);
    }

    public function getCleanCpf(): ?string
    {
        if (!$this->cpf) {
            return null;
        }

        return preg_replace('/[^0-9]/', '', $this->cpf);
    }

    public function getActivityDescription(): string
    {
        return 'Usuário alterou seus dados de perfil.';
    }

    public function getSuccessMessage(): string
    {
        return 'Dados atualizados com sucesso!';
    }

    public function validateRequiredFields(): bool
    {
        return !empty($this->name);
    }

    public function validateForRegistration(): bool
    {
        return !empty($this->name) &&
               !empty($this->email) &&
               !empty($this->password) &&
               !empty($this->cpf) &&
               !empty($this->birthdate) &&
               !empty($this->securityAnswer);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'cpf' => $this->cpf,
            'birthdate' => $this->birthdate,
            'security_answer' => $this->securityAnswer,
        ];
    }
}
