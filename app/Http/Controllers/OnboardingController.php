<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function show(Organization $organization): View
    {
        $this->authorize('view', $organization);

        return view('onboarding.show', [
            'organization' => $organization,
            'tenant' => $organization->tenants()->first(),
            'domain' => $organization->domains()->where('is_primary', true)->first(),
        ]);
    }
}
