<?php

namespace App\Services;

use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Contracts\PartyContract;
use App\Traits\PrescriptionInvoiceHelpers;
use App\Classes\PrescriptionItem;
use Illuminate\Support\Collection;

class PrescriptionInvoice extends Invoice
{
    use PrescriptionInvoiceHelpers;
    /**
     * @var PartyContract
     */
    public $patient;

    /**
     * @var string
     */
    public $patientId;

    /**
     * @var Collection
     */
    public $prescriptions;

    public function __construct($name = '')
    {
        parent::__construct($name);
        $this->prescriptions = Collection::make([]);
    }

    /**
     * @return PrescriptionItem
     */
    public static function makeItem(string $title = '')
    {
        return(new PrescriptionItem())->title($title);
    }

    /**
     * @return $this
     */
    public function addPrescription(PrescriptionItem $prescription)
    {
        $this->prescriptions->push($prescription);

        return $this;
    }

    /**
     * Add multiple prescriptions to the invoice.
     *
     * @param array|Collection $prescriptions
     * @return $this
     */
    public function addPrescriptions($prescriptions)
    {
        foreach ($prescriptions as $prescription) {
            $this->addPrescription($prescription);
        }

        return $this;
    }

    /**
     * Get the total amount for all prescriptions.
     *
     * @return float
     */
    public function getTotalAmount(): float
    {
        return $this->prescriptions->sum(function ($prescription) {
            return $prescription->getTotalPrice();
        });
    }

    /**
     * Get the prescription count.
     *
     * @return int
     */
    public function getPrescriptionCount(): int
    {
        return $this->prescriptions->count();
    }

    /**
     * Clear all prescriptions from the invoice.
     *
     * @return $this
     */
    public function clearPrescriptions()
    {
        $this->prescriptions = Collection::make([]);
        return $this;
    }
}