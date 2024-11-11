<div>
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Prescriptions</h2>
            <button class="btn btn-primary">New Prescription</button>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Doctor</th>
                        <th>Diagnosis</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescriptions as $prescription)
                        <tr>
                            <td>{{ $prescription->prescription_date }}</td>
                            <td>{{ $prescription->doctor->name }}</td>
                            <td>{{ $prescription->diagnosis }}</td>
                            <td>{{ $prescription->status }}</td>
                            <td>
                                <button class="btn btn-sm">View</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 