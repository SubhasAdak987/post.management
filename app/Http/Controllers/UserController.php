<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Post;
use App\Models\PostComment;

class UserController extends Controller
{
    public function dashboard(){
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

        return view('dashboard', compact('posts'));
    }
    // --- GOOGLE ---
    public function redirectToGoogle(){
        try{
            return Socialite::driver('google')->redirect();
        }
        catch(Exception $e){
            Log::error("Error to Redirect to google". $e->getMessage());
            return redirect()->route('login')->withErrors(['google_error' => 'Error to Redirect to google']);
        }
    }

    public function handleGoogleCallback(){
        try{
            // $googleUser = Socialite::driver('google')->user();
            $googleUser = Socialite::driver('google')->stateless()->user();

            $check = User::where('email', $googleUser->getEmail())->first();
            if(!$check || $check->type == 'user') {
                $user = User::updateOrCreate(
                    ['email' => $googleUser->getEmail()],
                    [
                        'type' => 'user',
                        'name' => $googleUser->getName(),
                        // 'google_id' => $googleUser->getId(),
                        'password' => bcrypt('default_password'),
                        'login_form' => 'google',
                    ]
                );
                Auth::login($user);
                return redirect('/dashboard');
            }
            else{
                return redirect()->route('login')->withErrors(['Login_error' => 'You are not a regular user']);
            }

        }
        catch(Exception $e){
            Log::error("Error to login with google". $e->getMessage());
            return redirect()->route('login')->withErrors(['google_error' => 'Error to login with Google']);
        }
    }

    // --- FACEBOOK ---
    public function redirectToFacebook(){
        try{
            return Socialite::driver('facebook')->redirect();
        }
        catch(Exception $e){
            Log::error("Error to Redirect to facebook". $e->getMessage());
            return redirect()->route('login')->withErrors(['facebook_error' => 'Error to Redirect to facebook']);
        }
    }

    public function handleFacebookCallback(){
        try{
            $facebookUser = Socialite::driver('facebook')->user();

            $check = User::where('email', $facebookUser->getEmail())->first();

            if(!$check || $check->type == 'user') {
                $user = User::updateOrCreate([
                    'email' => $facebookUser->getEmail(),
                ],
                [
                    'type' => 'user',
                    'name' => $facebookUser->getName(),
                    // 'facebook_id' => $facebookUser->getId(),
                    'password' => bcrypt('default_password'),
                    'login_form' => 'facebook',
                ]);

                Auth::login($user);
                return redirect('/dashboard');
            }
            else{
                return redirect()->route('login')->withErrors(['Login_error' => 'You are not a regular user']);
            }
        }
        catch(Exception $e){
            Log::error("Error to login with facebook". $e->getMessage());
            return redirect()->route('login')->withErrors(['google_error' => 'Error to login with facebook']);
        }
    }

    public function RegisterSubmit(Request $req){
        try{
            $data   = $req->validate([
                'name'      => 'required|string|max:255',
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|confirmed|min:6',
            ]);
            $data['type'] = 'user';
            $user   = User::create($data);

            if($user){
                // $req->session()->regenerate();
                Auth::login($user);
                return redirect('/dashboard');
            }
        }
        catch(Exception $e){
            Log::error("Error to regiester user data". $e->getMessage());
            return redirect()->route('login')->withErrors(['register_error' => 'Error to regiester user data. try after sometime']);
        }
    }

    public function LoginCheck(Request $req){
        try{
            $req->validate([
                'email'     => 'required|email',
                'password'  => 'required',
            ]);
            // dd($req);

            $user = User::where(['type'=>'user','email'=> $req->email])->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Email not found.',])->onlyInput('email');
            }

            if (!Hash::check($req->password, $user->password)) {
                return back()->withErrors(['password' => 'Password is incorrect.',])->onlyInput('email');
            }
            Auth::login($user);
            $req->session()->regenerate();
            return redirect('/dashboard');
        }
        catch(Exception $e){
            Log::error("Error to user login". $e->getMessage());
            return redirect()->route('login')->withErrors(['login_error' => 'Error to user login. try after sometime']);
        }
    }
}
