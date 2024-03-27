<?php

namespace App\Traits;

use LaravelDaily\Invoices\Contracts\PartyContract;
trait PrescriptionInvoiceHelpers
{
    /**
     * @return $this
     */
    public function patient(PartyContract $patient)
    {
        $this->patient = $patient;

        return $this;
    }

    public function patientId($id)
    {
        $this->patientId = $id;

        return $this;
    }
}
