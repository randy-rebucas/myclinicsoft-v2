<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Laravel\Nova\Actions\ActionResponse;

class PrintPrescriptionPDF extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $medication = $models->first();
        // $pdf = PDF::loadView('pdfs.prescription', [
        //     'medication' => $medication,
        //     'patient' => $medication->patient,
        //     'items' => $medication->prescription_items,
        // ])
        //     ->setOptions([
        //         'isHtml5ParserEnabled' => true,
        //         'isRemoteEnabled' => true,
        //         'defaultFont' => 'sans-serif',
        //         'isPhpEnabled' => true,
        //         'isFontSubsettingEnabled' => true,
        //         'defaultCharset' => 'utf-8'
        //     ]);

        // return Action::download($pdf->output(), 'prescription-' . $medication->id . '.pdf');
        return Action::downloadUrl('Prescription', function ($medication) {
            return route('prescription', $medication->encounter_id);
        })->standalone();
        // return $pdf->download('prescription-' . $medication->id . '.pdf');
        // return ActionResponse::download(
        //     'prescription-' . $medication->id . '.pdf',
        //     $pdf->output()
        // );
        // return Action::streamDownload(
        //     'prescription-' . $medication->id . '.pdf',
        //     $pdf->output()
        // );
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
