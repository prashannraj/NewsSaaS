<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use App\Services\OrganizationService;
use Illuminate\Http\RedirectResponse;

class OrganizationController extends Controller
{
    public function __construct(protected OrganizationService $organizations) {}

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $organization = $this->organizations->create($request->user(), $request->validated());

        return redirect()->route('onboarding.show', $organization)->with('status', 'organization-created');
    }
}
