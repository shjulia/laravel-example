<?php

namespace App\Http\Controllers\Admin\Users;

use App\Entities\User\SignupAutosave;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class AutoSaveController
 * @package App\Http\Controllers\Admin\Users
 */
class AutoSaveController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = SignupAutosave::orderBy('updated_at', 'desc');
        if (!empty($value = $request->get('first_name'))) {
            $query->where('first_name', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('last_name'))) {
            $query->where('last_name', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('email'))) {
            $query->where('email', 'like', '%' . $value . '%');
        }
        $users = $query->paginate();
        return view('admin.users.autosave', compact('users'));
    }

    /**
     * @param SignupAutosave $potUser
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(SignupAutosave $potUser)
    {
        if ($potUser->id) {
            $potUser->delete();
            return redirect()->back()->with('success', 'Auto save removed successfully');
        }
        return redirect()->back()->with('error', 'Auto save not found');
    }
}
