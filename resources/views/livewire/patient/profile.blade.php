<?php

use App\Models\Patient;
use function Livewire\Volt\{state};

state('patient');

?>
<div class="w-full mx-auto p-8">
    <!-- Add Medications Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Add Medications</h2>
        <form class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Medication Name</label>
                    <input type="text" name="medication_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Enter medication name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dosage</label>
                    <input type="text" name="dosage" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Enter dosage">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Instructions</label>
                <textarea name="instructions" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Enter instructions"></textarea>
            </div>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Add Medication</button>
        </form>
    </div>

    <!-- Add Patient Records Section -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Add Patient Records</h2>
        <form class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Record Type</label>
                    <select name="record_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option>Choose a record type</option>
                        <option>Allergy</option>
                        <option>Diagnosis</option>
                        <option>Treatment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="record_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Details</label>
                <textarea name="details" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Enter details"></textarea>
            </div>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Add Record</button>
        </form>
    </div>
</div>
