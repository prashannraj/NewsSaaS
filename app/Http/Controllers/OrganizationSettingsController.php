<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrganizationSettingsRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizationSettingsController extends Controller
{
    public function edit(Organization $organization): View
    {
        $this->authorize('update', $organization);

        return view('organizations.settings', compact('organization'));
    }

    public function update(Organization $organization, UpdateOrganizationSettingsRequest $request): RedirectResponse
    {
        $this->authorize('update', $organization);

        $organization->update($request->validated());

        return redirect()->back()->with('status', 'organization-updated');
    }
}
