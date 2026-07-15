<?php

namespace App\Http\Controllers\CampusUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campus\CampusUserRequest;
use App\Models\Campus;
use App\Models\User;
use App\Services\UserCampusServices;
use App\Traits\CampusListTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    use CampusListTrait;

    protected $userCampusServices;

    public function __construct(UserCampusServices $userCampusServices)
    {
        $this->userCampusServices = $userCampusServices;
    }

    public function index(Request $request): Response
    {
        $users = $this->userCampusServices->index($request);

        return Inertia::render('CampusUser/List', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        $roles = $this->userCampusServices->getTenantRoles();

        return Inertia::render('CampusUser/Create', [
            'roles' => $roles,
        ]);
    }

    public function submit(CampusUserRequest $request): RedirectResponse
    {
        $this->userCampusServices->submit($request);

        return $this->redirectSuccess('User created successfully!', 'user.index');
    }

    public function edit(Request $request): Response
    {
        $roles = $this->userCampusServices->getTenantRoles();
        $user = $this->findUser($request->id);

        return Inertia::render('CampusUser/Edit', [
            'users' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(CampusUserRequest $request): RedirectResponse
    {
        $this->userCampusServices->update($request);

        return $this->redirectSuccess('User updated successfully!', 'user.index');
    }

    public function delete(Request $request)
    {
        $userData = $this->userCampusServices->delete($request);
        // You cannot delete your own account.
        if ($userData == false) {
            return $this->redirectError('You cannot delete your own account!', 'user.index');
        } else {
            return $this->redirectSuccess('User deleted successfully!', 'user.index');
        }
    }

    public function getCampusData(): array|Collection
    {
        $previousSelectedCampusId = session('selected_campus_id');
        if ($previousSelectedCampusId) {
            $data['previousSelectedCampusId'] = $previousSelectedCampusId;
        } else {
            $data['previousSelectedCampusId'] = null;
        }
        $data['getCampusList'] = $this->getCampusList();

        return $data;
    }

    public function switchCampus(Request $request): void
    {

        $campusData = Campus::select('id', 'tenant_id')->where('id', $request->campus_id)->first();
        session(['selected_campus_id' => $request->campus_id]);
        session(['switched_tenant_id' => $campusData->tenant_id]);
    }

    private function findUser(int $id)
    {
        return User::with('roles')->findOrFail($id);
    }
}
