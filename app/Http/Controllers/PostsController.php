<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Post;
use App\Models\PostComment;

class PostsController extends Controller
{
    public function PostStore(Request $req){
        try{
            $req->validate([
                'title'   => 'required|string|max:255',
                'content' => 'nullable|string|required_without:file',
                'file'    => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:5120',
            ]);

            $filetype   = NULL;
            $user = auth()->id();
            //  dd($user);
            $data['user_id'] = $user;
            $data['title'] = $req->title;

            if($req->hasFile('file')){
                $storeFolder = "Posts/{$user}";
                $path = $req->file('file')->store($storeFolder, 'public');
                $data['file_path'] = $path;
                $data['file_type'] = 'file';
            }else{    
                    $data['file_path'] = $req->content;
                    $data['file_type'] = 'text';
            }
            $data['created_at'] = now();
            Post::create($data);
            return redirect()->back()->with('success', 'Post created successfully!');
        }
        catch(Exception $e){
            Log::error("Error to Save a post". $e->getMessage());
            return redirect()->route('dashboard')->with(['error' => 'Error to save a post']);
        }
    }
    public function PostsEdit($id){
        try{
            $data   = Post::where('id',$id)->first();

            if(!$data){
                return redirect()->route('dashboard')->withErrors(['error' => 'Post data not found']);
            }
            return view('posts.edit', compact('data'));
        }
        catch(Exception $e){
            Log::error("Error to edite a post". $e->getMessage());
            return redirect()->route('dashboard')->with(['error' => 'Error to edite a post']);
        }
    }
    public function PostUpdate(Request $req,$id){
        try{
            $req->validate([
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
                'file' => 'nullable|mimes:jpg,jpeg,png,mp4|max:5120', // Max 5MB
            ]);

            $post = Post::where('id', $id)->first();

            if (!$post) {
                return redirect()->route('dashboard')->withErrors(['error' => 'Post not found']);
            }

            $filePath = $post->file_path;
            $fileType = $post->file_type;

            if($fileType == 'file'){
                if ($req->hasFile('file')) {
                    if ($post->file_path && Storage::disk('public')->exists($post->file_path)) {
                        Storage::disk('public')->delete($post->file_path);
                    }

                    $file       = $req->file('file');
                    $filePath   = $file->store('Posts/' . auth()->id(), 'public');
                }
            }
            else{
                $filePath   = $req->content;
            }

            Post::where('id', $id)
            ->update([
                'title'         => $req->title,
                'file_path'     => $filePath,
                'file_type'     => $fileType,
                'updated_at'    => now(),
                'updated_by'    => auth()->id()
            ]);

            return redirect()->route('dashboard')->with('success', 'Post updated successfully!');
        }
        catch(Exception $e){
            Log::error("Error to update a post". $e->getMessage());
            return redirect()->route('dashboard')->with(['error' => 'Error to update a post']);
        }
        
    }
    public function PostsComment(Request $req, $id){
        try{
            $req->validate([
                'comment'   => 'required|string|max:255',
            ]);

            $user = auth()->id();
            $data['post_id']        = $id;
            $data['user_id']        = $user;
            $data['post_comments']  = $req->comment;
            $data['created_at']     = now();
            $insert = PostComment::create($data);
            return redirect()->back()->with('success', 'Comment posted successfully!');
        }
        catch(Exception $e){
            Log::error("Error to comment in a post". $e->getMessage());
            return redirect()->route('dashboard')->with(['error' => 'Error to comment in a post']);
        }
    }
    public function PostsDelete($id){
        try{
            $delete = Post::where('id',$id)
                    ->update([
                        'deleted_at'    => now(),
                        'delete_by'     => auth()->id()
                    ]);
            if($delete){
                return redirect()->back()->with('success', 'Post deleted successfully!');
            }
            else{
                return redirect()->back()->with('error', 'faield to delete post!');
            }
        }
        catch(Exception $e){
            Log::error("Error to delete a post". $e->getMessage());
            return redirect()->route('dashboard')->with(['error' => 'Error to delete a post']);
        }
    }
    public function CommentEdit($id){
        try{
            $data   = PostComment::where('id',$id)->first();

            if(!$data){
                return redirect()->route('dashboard')->withErrors(['error' => 'Comment data not found']);
            }
            return view('posts.comment_edit', compact('data'));
        }
        catch(Exception $e){
            Log::error("Error to edite a Comment". $e->getMessage());
            return redirect()->route('dashboard')->with(['error' => 'Error to edite a Comment']);
        }
    }
    public function CommentUpdate(Request $req,$id){
        try{
            $req->validate([
                'comment' => 'required|string|max:255',
            ]);

            $post = PostComment::where('id', $id)->first();

            if (!$post) {
                return redirect()->route('dashboard')->withErrors(['error' => 'Comment not found']);
            }

            $filePath = $post->file_path;
            $fileType = $post->file_type;

            PostComment::where('id', $id)
            ->update([
                'post_comments' => $req->comment,
                'updated_at'    => now(),
                'updated_by'    => auth()->id()
            ]);

            return redirect()->route('dashboard')->with('success', 'Comment updated successfully!');
        }
        catch(Exception $e){
            Log::error("Error to update a post". $e->getMessage());
            return redirect()->route('dashboard')->with(['error' => 'Error to update a Comment']);
        }
        
    }
    public function CommentDelete($id){
        try{
            $delete = PostComment::where('id',$id)
                    ->update([
                        'deleted_at'    => now(),
                        'delete_by'     => auth()->id()
                    ]);
            if($delete){
                return redirect()->back()->with('success', 'Comment deleted successfully!');
            }
            else{
                return redirect()->back()->with('error', 'faield to delete comment');
            }
        }
        catch(Exception $e){
            Log::error("Error to delete a comment". $e->getMessage());
            return redirect()->route('dashboard')->with(['error' => 'Error to delete a comment']);
        }
    }
}
