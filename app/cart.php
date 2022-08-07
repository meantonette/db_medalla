<?php

namespace App;

class cart
{
    
    public $services = null;
    public $pets = null;
    public $totalPrice = 0;

    public function __construct($oldCart)
    {
        if ($oldCart) {
            $this->services = $oldCart->services;
         
            $this->totalPrice = $oldCart->totalPrice;
        
            $this->pets = $oldCart->pets;
           
        }
    }

    public function add($services, $id)
    {
        try {
        $storedServices = ['price' => $services->price, 'services' => $services];
        if ($this->services) {
            if (array_key_exists($id, $this->services)) {

                $storedServices = array_unique($id);
            }
        }

        $storedServices['price'] = $services->price;
        $this->services[$id] = $storedServices;
        $this->totalPrice += $services->price;
    } catch (\Throwable $e) {
        return redirect()
            ->route("data")
            ->with("error", $e->getMessage());
    }
    }

    public function addPet($pets, $id)
    {
        try {
        $addPet = ['petName' => $pets->petName, 'animals' => $pets];
        if ($this->pets) {
            if (array_key_exists($id, $this->pets)) {

                $addPet = array_unique($id);
            }
        }
        $this->pets[$id] = $addPet;
         } catch (\Throwable $e) {
            return redirect()
                ->route("data")
                ->with("error", $e->getMessage());
        }
    }

    public function removeService($id)
    {
        $this->totalPrice -= $this->services[$id]['price'];
        unset($this->services[$id]);
        unset($this->pets[$id]);
    }
}
