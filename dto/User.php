<?php

namespace dto;

use JsonSerializable;

class User implements JsonSerializable
{
    public string $name;
    public string $surname;
    public string $email;
    public string $phone;
    public string $memberNo;
    public string $identityNumber;
    public string $location;
    public string $job;
    public string $active;
    public string $access_token;

    public function __construct(array $data)
    {
        $this->name = isset($data["k_adi"]) ? $data["k_adi"] : '' ;
        $this->surname = isset($data["k_soyadi"]) ? $data["k_soyadi"] : '' ;
        $this->email = isset($data["eposta"]) ? $data["eposta"] : '' ;
        $this->phone = isset($data["tel_no"]) ? $data["tel_no"] : '' ;
        $this->memberNo = isset($data["uye_no"]) ? $data["uye_no"] : '' ;
        $this->identityNumber = isset($data["kimlik_no"]) ? $data["kimlik_no"] : '' ;
        $this->location = isset($data["branch_name"]) ? $data["branch_name"] : '' ;
        $this->job = isset($data["kadro_unv"]) ? $data["kadro_unv"] : '' ;
        $this->active = isset($data["durumu"]) ? $data["durumu"] : '' ;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'phone' => $this->phone,
            'memberNo' => $this->memberNo,
            'identityNumber' => $this->identityNumber,
            'location' => $this->location,
            'job' => $this->job,
            'active' => $this->active
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
