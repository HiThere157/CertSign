<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Permission;

class PermissionController extends Controller
{
    //GET: index page to change the owner of a certificate
    public function permission_index($id)
    {
        $all_users = User::all();
    
        $certificate = Certificate::find($id);
        if($certificate && Gate::allows('owns-cert', $certificate)){
            return view('pages.permissions', [
                'id' => $id,
                'self_signed' => $certificate->self_signed,
                'all_users' => $all_users,
                'user_permissions' => Permission::where('certificate_id', $id)->get()
            ]);
        }

        return redirect()->route('certificates');
    }
    
    //POST: change the owner of a certificate
    public function changeOwner(Request $request, $id)
    {
        $this->validate($request, [
            'newOwner' => 'required'
        ]);
    
        $certificate = Certificate::find($id);
        if($certificate){
            if(!Gate::allows('owns-cert', $certificate)){
                return redirect()->route('certificates')->withErrors([
                    'error' => 'No Permission! Only the owner of this certificate can change the owner.'
                ]);
            }
    
            $certificate->owner_id = $request->input('newOwner');
            $certificate->save();
        }
    
        return redirect()->route('certificates');
    }

    //POST: add a permission to a certificate
    public function add(Request $request, $id)
    {
        $this->validate($request, [
            'addUser' => 'required'
        ]);
    
        $certificate = Certificate::find($id);
        if($certificate && $certificate->self_signed){
            if(!Gate::allows('owns-cert', $certificate)){
                return redirect()->route('certificates')->withErrors([
                    'error' => 'No Permission! Only the owner of this certificate can add permissions.'
                ]);
            }
    
            $user = User::find($request->input('addUser'));
            if($user && Permission::where('certificate_id', $id)->where('user_id', $user->id)->count() == 0){
                $permission = new Permission();
                $permission->user_id = $user->id;
                $permission->added_by_id = auth()->user()->id;
                $permission->certificate_id = $certificate->id;
                $permission->save();
            }
        }
    
        return redirect()->route('permissions', $id);
    }

    //GET: delete a permission from a certificate
    public function delete($id)
    {
        $permission = Permission::find($id);
        if($permission){
            if(!Gate::allows('owns-cert', $permission->certificate)){
                return redirect()->route('certificates')->withErrors([
                    'error' => 'No Permission! Only the owner of this certificate can delete permissions.'
                ]);
            }
    
            $permission->delete();
        }
    
        return redirect()->route('permissions', $permission->certificate->id);
    }
}