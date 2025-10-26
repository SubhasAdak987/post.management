<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Post;
use App\Models\PostComment;

class AdminPostController extends Controller
{
    public function AdminPostsEdit($id){
        try{
            $data   = Post::where('id',$id)->first();

            if(!$data){
                return redirect()->route('post.list')->withErrors(['error' => 'Post data not found']);
            }
            return view('admin.posts.edit', compact('data'));
        }
        catch(Exception $e){
            Log::error("Error to edite a post". $e->getMessage());
            return redirect()->route('post.list')->with(['error' => 'Error to edite a post']);
        }
    }
    public function AdminPostUpdate(Request $req,$id){
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

            return redirect()->route('post.list')->with('success', 'Post updated successfully!');
        }
        catch(Exception $e){
            Log::error("Error to update a post". $e->getMessage());
            return redirect()->route('dashboard')->with(['error' => 'Error to update a post']);
        }
        
    }
    public function AdminPostsComment(Request $req, $id){
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
            return redirect()->route('post.list')->with(['error' => 'Error to comment in a post']);
        }
    }
    public function AdminPostsDelete($id){
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
            return redirect()->route('post.list')->with(['error' => 'Error to delete a post']);
        }
    }
    public function AdminCommentEdit($id){
        try{
            $data   = PostComment::where('id',$id)->first();

            if(!$data){
                return redirect()->route('post.list')->withErrors(['error' => 'Comment data not found']);
            }
            return view('Admin.posts.comment_edit', compact('data'));
        }
        catch(Exception $e){
            Log::error("Error to edite a Comment". $e->getMessage());
            return redirect()->route('post.list')->with(['error' => 'Error to edite a Comment']);
        }
    }
    public function AdminCommentUpdate(Request $req,$id){
        try{
            $req->validate([
                'comment' => 'required|string|max:255',
            ]);

            $post = PostComment::where('id', $id)->first();

            if (!$post) {
                return redirect()->route('post.list')->withErrors(['error' => 'Comment not found']);
            }

            $filePath = $post->file_path;
            $fileType = $post->file_type;

            PostComment::where('id', $id)
            ->update([
                'post_comments' => $req->comment,
                'updated_at'    => now(),
                'updated_by'    => auth()->id()
            ]);

            return redirect()->route('post.list')->with('success', 'Comment updated successfully!');
        }
        catch(Exception $e){
            Log::error("Error to update a post". $e->getMessage());
            return redirect()->route('dashboard')->with(['error' => 'Error to update a Comment']);
        }
        
    }
    public function AdminCommentDelete($id){
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
            return redirect()->route('post.list')->with(['error' => 'Error to delete a comment']);
        }
    }
}
