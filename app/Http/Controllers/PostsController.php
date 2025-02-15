<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class PostsController extends Controller
{
    

    public function index(){
        return view('post', [
            'postsAlreadySent' => Post::where('published_at', '!=', null)->get(),
            'postsToBeSent' => Post::postsToBeSent()
        ]);
    }

    public function confirmPost(int $postId){
        $post = Post::find($postId);
        return view('post-create', [
            'post' => $post
        ]);
    }

    public function fetchFromWordpress()
    {
        Post::fetchFromWordpress();
        Session::flash('successMessage', 'Posts successfully synced with WordPress.');
        return redirect('/posts');
    }

    public function list()
    {
        $posts = Post::all();
        return response()->json([
            'meta' => [
                'count' => count($posts)
            ],
            'data' => $posts
        ]);
    }

    public function get(string $postId)
    {
        $post = Post::findOrFail($postId);
        return response()->json([
            'meta' => [
                'message' => 'Post fetched successfully'
            ],
            'data' => $post
        ]);
    }

    public function update(Request $request, string $postId)
    {
        $post = Post::findOrFail($postId);
        $post->update($request->all());
        $post->save();

        return response()->json([
            'meta' => [
                'message' => 'Post updated successfully',
                'attributes_changed' => $request->all()
            ],
            'data' => $post
        ]);
    }

    public function manuallyPostToLinkedIn()
    {
        $postsCreatedSuccessfully = Post::postToLinkedIn();
        if($postsCreatedSuccessfully){
            Session::flash('successMessage', 'Posts were successfully sent to LinkedIn.'); 
        } else {
            Session::flash('errorMessage', 'Something went wrong. Posts were not sent to LinkedIn.'); 
        }

        return redirect('/posts');
    }

    public function manuallyMarkAllPublished()
    {
        $count = Post::where('published_at', null)->update(['published_at' => Carbon::now()]);
        
        if($count > 0){
            Session::flash('successMessage', 'Posts all marked as published.');
        } else {
            Session::flash('successMessage', 'No posts to publish.');
        }
        
        return redirect('/posts');
    }
}
