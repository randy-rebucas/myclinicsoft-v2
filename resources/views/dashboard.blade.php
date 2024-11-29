@can('manage-system')
    <div class="admin-panel">
        <!-- Admin specific content -->
    </div>
@endcan

@can('manage-appointments')
    <div class="appointment-manager">
        <!-- Appointment management content -->
    </div>
@endcan

@can('view-medical-records')
    <div class="medical-records">
        <!-- Medical records content -->
    </div>
@endcan
