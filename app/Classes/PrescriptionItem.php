<?php

namespace App\Classes;

class PrescriptionItem
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string|bool
     */
    public $description = false;

    /**
     * @var float
     */
    public $quantity;

    /**
     * @var float
     */
    public $pricePerUnit = 0.0;

    /**
     * @var float
     */
    public $subTotalPrice = 0.0;

    /**
     * InvoiceItem constructor.
     */
    public function __construct()
    {
        $this->quantity = 1.0;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function title(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function description(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param float $quantity
     * @return $this
     */
    public function quantity(float $quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @param float $pricePerUnit
     * @return $this
     */
    public function pricePerUnit(float $pricePerUnit)
    {
        $this->pricePerUnit = $pricePerUnit;

        return $this;
    }

    /**
     * @param float $subTotalPrice
     * @return $this
     */
    public function subTotalPrice(float $subTotalPrice)
    {
        $this->subTotalPrice = $subTotalPrice;

        return $this;
    }

    /**
     * Get the total price for this prescription item
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->subTotalPrice > 0 ? $this->subTotalPrice : ($this->pricePerUnit * $this->quantity);
    }

}
