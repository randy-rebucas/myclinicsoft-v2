<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'check.subscription'])->group(function () {
    // Protected routes that require subscription
});
