<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\StatisticsService;
use App\Models\User;
use App\Models\Post;
use App\Models\PostComment;

class AdminController extends Controller
{
    public function AdminLoginCheck(Request $req){
        try{
            $req->validate([
                'email'     => 'required|email',
                'password'  => 'required',
            ]);
            // dd($req);

            $user = User::where(['type'=>'admin','email'=> $req->email])->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Email not found.',])->onlyInput('email');
            }

            if (!Hash::check($req->password, $user->password)) {
                return back()->withErrors(['password' => 'Password is incorrect.',])->onlyInput('email');
            }
            // Auth::login($user);
            if (Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
                // return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
                $req->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
        }
        catch(Exception $e){
            Log::error("Error to Admin login". $e->getMessage());
            return redirect()->route('login')->withErrors(['login_error' => 'Error to Admin login. try after sometime']);
        }
    }
    public function AdminDashboard(StatisticsService $stats){
        $overview = $stats->getOverview();

        return view('Admin.dashboard', [
            'totalUsers' => $overview['users'],
            'totalPosts' => $overview['posts'],
            'totalComments' => $overview['comments'],
        ]);


        // $totalPosts = Post::whereNull('deleted_at')->count();
        // $totalUsers = User::count();
        // $totalComments = PostComment::whereNull('deleted_at')->count();

        // return view('Admin.dashboard', compact('totalPosts', 'totalUsers', 'totalComments'));
    }
    public function UsersList(){
        $data   = User::orderBy('created_at', 'desc')->get();

        return view('Admin.users.list', compact('data'));
    }
    public function AdminUsersEdit($id){
        $user = User::findOrFail($id);
        return view('Admin.users.edit', compact('user'));
    }
    public function AdminUsersUpdate(Request $req, $id){
        try{
            $req->validate([
                'name' => 'required|string|max:255',
                'password' => 'nullable|string|min:6',
                'type' => 'required|in:admin,user',
            ]);

            $user = User::findOrFail($id);
            $user->name = $req->name;
            $user->type = $req->type;

            if ($req->password) {
                $user->password = Hash::make($req->password);
            }

            $user->save();

            return redirect()->route('users.list')->with('success', 'User updated successfully.');
        }
        catch(Exception $e){
            Log::error("Error to update user data". $e->getMessage());
            return redirect()->route('dashbord')->withErrors(['login_error' => 'Error to update user data. try after sometime']);
        }
    }
    public function AdminUsersDelete($id){
        User::where('id', $id)->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function PostList(){
        $posts  = DB::table('posts as po')
                ->join('users', 'po.user_id', '=', 'users.id')
                ->select(
                    'po.id as PostId',
                    'users.id as UserId',
                    'users.name as UserName',
                    'po.title as PostTitle',
                    'po.file_path as PostFile',
                    'po.file_type as FileType',
                    'po.created_at',
                )
                ->whereNull('po.deleted_at')
                ->orderBy('po.id','desc')
                ->get();
        foreach ($posts as $post) {
            $post->PostComment = DB::table('post_comments as pc')
                ->join('users as u', 'pc.user_id', '=', 'u.id')
                ->where('pc.post_id', $post->PostId)
                ->select(
                    'pc.id as PCId',
                    'u.name as UserName',
                    'pc.user_id as PCUid',
                    'pc.post_comments as Comment'
                )
                ->whereNull('pc.deleted_at')
                ->get();
        }

        return view('Admin.posts.list', compact('posts'));
    }
}
