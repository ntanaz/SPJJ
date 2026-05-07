<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Permissions berhasil diperbarui untuk role ' . $role->name);
    }
}
