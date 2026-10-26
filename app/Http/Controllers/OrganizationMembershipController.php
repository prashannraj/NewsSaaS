<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationMembership;
use Illuminate\Http\RedirectResponse;

class OrganizationMembershipController extends Controller
{
    public function update(Organization $organization, OrganizationMembership $membership)
    {
        $this->authorize('manage', $organization);
        abort_unless($membership->organization_id === $organization->id, 404);

        $data = request()->validate([
            'membership_status' => ['required', 'in:active,suspended'],
            'role_id' => ['nullable', 'integer'],
        ]);

        $membership->update(['membership_status' => $data['membership_status']]);

        if (! empty($data['role_id'])) {
            $role = $organization->roles()->findOrFail($data['role_id']);
            $membership->roles()->sync([$role->id]);
        }

        return redirect()->back()->with('status', 'membership-updated');
    }

    public function destroy(Organization $organization, OrganizationMembership $membership): RedirectResponse
    {
        $this->authorize('manage', $organization);
        abort_unless($membership->organization_id === $organization->id, 404);

        if ($membership->user_id === $organization->owner_user_id) {
            abort(403, 'Cannot remove organization owner.');
        }

        $membership->delete();

        return redirect()->back()->with('status', 'membership-removed');
    }
}
