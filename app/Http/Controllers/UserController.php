<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Mail\UserStored;
use App\Mail\UserUpdated;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Custom Authorize the user for this action
     *
     * @return bool
     */
    public function auth(User $user)
    {
        $authed = false;
        switch (Auth::user()->role) {
            case 'super':
                $authed = true;
                break;
            case 'admin':
                if ($user->role != 'super') {
                    $authed = true;
                } else {
                    $authed = false;
                }
                break;
            case 'moderator':
                if (!(in_array($user->role, ['super', 'admin', 'moderator']))) {
                    $authed = true;
                } else {
                    $authed = false;
                }
            default:
                $authed = false;
                break;
        }
        return $authed;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $locale, $showDisabled = false)
    {
        if ($showDisabled) {
            $users = User::all();
        } else {
            $users = User::where('disabled', false)->get();
        }

        $roles = ['super' => 'Webmaster', 'admin' => 'ITSB Administrator', 'moderator' => 'ITSB Staff', 'auditor' => 'DPO Data Protection Officer', 'user' => 'DPC Data Privacy Coordinator'];
        return view('models.users.index', compact('roles', 'users', 'showDisabled'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        // make roles available as per user role
        $roles = [];
        switch (Auth::user()->role) {
            case 'super':
                $roles = ['Webmaster' => 'super', 'ITSB Administrator' => 'admin', 'ITSB Staff' => 'moderator', 'DPO Data Protection Officer' => 'auditor', 'DPC Data Privacy Coordinator' => 'user'];
                break;
            case 'admin':
                $roles = ['ITSB Administrator' => 'admin', 'ITSB Staff' => 'moderator', 'DPO Data Protection Officer' => 'auditor', 'DPC Data Privacy Coordinator' => 'user'];
                break;
            case 'moderator':
                $roles = ['DPO Data Protection Officer' => 'auditor', 'DPC Data Privacy Coordinator' => 'user'];
                break;
            default:
                $roles = [];
                break;
        }
        // expose organisations as per user role
        $organisations = Organisation::all();
        return view('models.users.create', compact(['roles', 'organisations']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store($locale, UserStoreRequest $request)
    {
        //
        $data = $request->validated();
        try {
            $password = $data['password'];
            $data['password'] = Hash::make($data['password']);
            $data['disabled'] = $data['disabled'] ?? false;
            $user = User::create($data);
            Mail::to($user->email)->send(new UserStored($user, $password));
        } catch (\Throwable $th) {
            return redirect()->route('users.index', App::currentLocale())->with('error', 'User created however, ' . $th->getMessage());
            //throw $th;
        }
        return redirect()->route('users.index', App::currentLocale())->with('success', __('messages.itemCreatedSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, User $user)
    {
        //
        if ($this->auth($user)) {
            $roles = [];
            switch (Auth::user()->role) {
                case 'super':
                    $roles = ['Webmaster' => 'super', 'ITSB Administrator' => 'admin', 'ITSB Staff' => 'moderator', 'DPO Data Protection Officer' => 'auditor', 'DPC Data Privacy Coordinator' => 'user'];
                    break;
                case 'admin':
                    $roles = ['ITSB Administrator' => 'admin', 'ITSB Staff' => 'moderator', 'DPO Data Protection Officer' => 'auditor', 'DPC Data Privacy Coordinator' => 'user'];
                    break;
                case 'moderator':
                    $roles = ['DPO Data Protection Officer' => 'auditor', 'DPC Data Privacy Coordinator' => 'user'];
                    break;
                default:
                    $roles = [];
                    break;
            }
            $organisations = Organisation::all();
            return view('models.users.edit', compact(['user', 'roles', 'organisations']));
        } else {
            return abort(403, 'Insufficient Role Permissions');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($locale, UserUpdateRequest $request, User $user)
    {
        //
        $data = $request->validated();
        $password = null;
        $email = null;
        if ($data['password'] == null) {
            unset($data['password']);
        } else {
            $password = $data['password'];
            $data['password'] = Hash::make($data['password']);
        }
        if ($data['email'] != $user->email) {
            $email = $data['email'];
        }
        $data['disabled'] = $data['disabled'] ?? false;
        try {
            $user->update($data);
            if ($password) {
                // send credentials email
                Mail::to($user->email)->send(new UserUpdated($user, $password, 'creds'));
            } elseif ($email) {
                // send email change email
                Mail::to($user->email)->send(new UserUpdated($user, $password, 'email'));
            }
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('users.index', App::currentLocale())->with('error', 'User updated however, ' . $th->getMessage());
        }

        return redirect()->route('users.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
