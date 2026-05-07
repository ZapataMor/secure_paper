<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with([
                'activePaidSubscription.paymentPlan:id,name',
            ])
            ->orderBy('name')
            ->orderBy('last_name')
            ->get([
                'id',
                'role',
                'name',
                'last_name',
                'email',
                'phone',
                'document_type',
                'document_number',
                'status',
                'created_at',
            ]);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }
}
