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

}
