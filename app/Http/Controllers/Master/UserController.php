<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //  public function __construct()
    // {
    //     $this->middleware('permission:view user', ['only' => ['index']]);
    //     $this->middleware('permission:create user', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:update user', ['only' => ['update', 'edit']]);
    //     $this->middleware('permission:updateProfile user', ['only' => ['updateProfile']]);
    //     $this->middleware('permission:delete user', ['only' => ['destroy']]);
    // }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with(['department', 'roles']);
            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('name', function ($user) {
                    $avatar = $user->avatar
                        ? asset('storage/' . $user->avatar)
                        : asset('assets/images/logo/sinarmeadow.png');

                    return '
                    <div class="d-flex align-items-center">
                        <div class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-dark">
                            <img src="' . $avatar . '" alt="avatar" class="img-fluid">
                        </div>
                        <p class="mb-0 ps-2">' . e($user->name) . '</p>
                    </div>
                ';
                })
                ->addColumn('roles', function ($user) {
                    $badges = '';
                    foreach ($user->roles as $role) {
                        $badges .= '<span class="badge bg-primary" style="margin-right:2px;">' . $role->name . '</span> ';
                    }
                    return $badges;
                })
                ->addColumn('department', function ($user) {
                    return $user->department ? $user->department->name : '-';
                })
                ->addColumn('action', function ($user) {
                    $roles = $user->roles->pluck('name')->toArray();
                    return '
                    <div class="flex justify-content-between gap-2">
                        <button type="button" class="btn btn-warning btn-edit-user"
                            data-id="' . $user->id . '"
                            data-nik="' . $user->nik . '"
                            data-username="' . $user->username . '"
                            data-name="' . $user->name . '"
                            data-email="' . $user->email . '"
                            data-department_id="' . $user->department_id . '"
                            data-roles=\'' . json_encode($roles) . '\'
                            data-status="' . $user->status . '"
                        >
                            <i class="fa-solid fa-pencil text-white"></i>
                        </button>
                        <form action="' . url('/users/' . $user->id . '/delete') . '" method="POST" class="delete-form" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt text-white"></i>
                            </button>
                        </form>
                    </div>
                ';
                })
                ->rawColumns(['action', 'roles', 'name'])
                ->make(true);
        }

        $departments = Department::all();
        $roles = Role::pluck('name', 'name')->all();

        return view('page.master.users.index', compact('roles', 'departments'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'nik' => 'required|min:4|max:6|unique:users,nik',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:20',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user = User::create([
            'nik' => $request->nik,
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'status' => 'active',
        ]);

        $user->syncRoles($request->roles);

        // Return JSON for AJAX
        return response()->json(['success' => true, 'message' => 'User created successfully!']);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nik' => 'required|min:4|max:6|unique:users,nik,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|max:20',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,non active',
            'department_id' => 'required|exists:departments,id',
        ]);

        $data = $request->only([
            'nik',
            'username',
            'name',
            'email',
            'position_id',
            'department_id',
            'status',
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete('avatar/' . $user->avatar);
            }

            $extension = $request->avatar->getClientOriginalExtension();
            $avatarName = $request->username . '.' . $extension;

            $request->avatar->storeAs('avatar', $avatarName, 'public');
            $data['avatar'] = $avatarName;
        }

        // Update user
        $user->update($data);

        // Sync roles
        $user->syncRoles($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!'
        ]);
    }


    public function destroy($userId)
    {
        $user = User::findOrFail($userId);

        // Hapus avatar jika ada
        if ($user->avatar) {
            Storage::delete('public/user_avatars/' . $user->avatar);
        }

        $user->delete();

        // Return JSON for AJAX
        return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
    }
}
