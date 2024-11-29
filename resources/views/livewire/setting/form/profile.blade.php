<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    // Removed activeTab property and setTab method
}; ?>

<section>
    <livewire:user.profile.update-profile-information-form />

    <livewire:user.profile.update-password-form />
</section>
