<?php

declare(strict_types=1);

namespace App\Entities\User\Practice;

/**
 * Class AddressDTO - Object with practice address data.
 * @package App\Entities\User\Practice
 */
class AddressDTO
{
    /**
     * @var string
     */
    public $practiceName;
    /**
     * @var string
     */
    public $address;
    /**
     * @var string
     */
    public $city;
    /**
     * @var string
     */
    public $state;
    /**
     * @var string
     */
    public $zip;
    /**
     * @var string|null
     */
    public $url;
    /**
     * @var string|null
     */
    public $practicePhone;
    /**
     * @var int|null
     */
    public $addressId;
    /**
     * @var float
     */
    public $lat;
    /**
     * @var float
     */
    public $lng;

    /**
     * AddressDTO constructor.
     * @param string $practiceName
     * @param string $address
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string|null $url
     * @param float $lat
     * @param float $lng
     * @param string|null $practicePhone
     * @param int|null $addressId
     */
    public function __construct(
        string $practiceName,
        string $address,
        string $city,
        string $state,
        string $zip,
        ?string $url,
        float $lat,
        float $lng,
        ?string $practicePhone = null,
        ?int $addressId = null
    ) {
        $this->practiceName = $practiceName;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
        $this->url = $url;
        $this->practicePhone = $practicePhone;
        $this->addressId = $addressId;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * @return string
     */
    public function fullAddress(): string
    {
        return explode(",", $this->address)[0]
            . ', ' . $this->city
            . ', ' . $this->state . ' ' . $this->zip
            . ', ' . 'USA';
    }

    /**
     * @return string
     */
    public function shortAddress(): string
    {
        return explode(",", $this->address)[0]
            . ', ' . $this->city;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode([
            'practiceName' => $this->practiceName,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'url' => $this->url,
            'practicePhone' => $this->practicePhone,
            'addressId' => $this->addressId,
            'lat' => $this->lat,
            'lng' => $this->lng
        ]);
    }
}
