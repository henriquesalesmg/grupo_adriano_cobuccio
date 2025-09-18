<?php

namespace App\DTOs;

class UserDTO
{
    public string $name;
    public string $email;
    public string $cpf;
    public string $birthdate;
    public string $security_answer;
    public string $password;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->cpf = $data['cpf'];
        $this->birthdate = $data['birthdate'];
        $this->security_answer = $data['security_answer'];
        $this->password = $data['password'];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'cpf' => $this->cpf,
            'birthdate' => $this->birthdate,
            'security_answer' => $this->security_answer,
            'password' => $this->password,
        ];
    }
}
