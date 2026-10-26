<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use App\Models\Organization;
use App\Services\InvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function __construct(protected InvitationService $invitations) {}

    public function index(Organization $organization): View
    {
        $this->authorize('inviteMembers', $organization);

        return view('invitations.index', [
            'organization' => $organization,
            'invitations' => $organization->invitations()->latest()->get(),
        ]);
    }

    public function store(Organization $organization, StoreInvitationRequest $request): RedirectResponse
    {
        $this->authorize('inviteMembers', $organization);

        $role = $request->filled('role_id')
            ? $organization->roles()->findOrFail($request->integer('role_id'))
            : null;

        $this->invitations->invite($organization, $request->string('email'), $role, $request->user());

        return redirect()->back()->with('status', 'invitation-sent');
    }

    public function resend(Invitation $invitation): RedirectResponse
    {
        $this->authorize('manage', $invitation->organization);

        $this->invitations->resend($invitation, request()->user());

        return redirect()->back()->with('status', 'invitation-resent');
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        $this->authorize('manage', $invitation->organization);

        $this->invitations->revoke($invitation, request()->user());

        return redirect()->back()->with('status', 'invitation-revoked');
    }

    public function show(string $token): View
    {
        return view('invitations.accept', ['token' => $token]);
    }

    public function accept(string $token): RedirectResponse
    {
        $this->invitations->accept($token, auth()->user());

        return redirect()->route('dashboard')->with('status', 'invitation-accepted');
    }
}
