<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paginate = Auth::user()->paginate;
        $isAdmin = Auth::user()->admin;
        $posts = Post::orderBy('created_at','desc')->paginate($paginate);
        return view('posts.index',compact('posts','isAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostRequest $postRequest
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $postRequest)
    {
        $isAdmin = Auth::user()->admin;
        $userId = Auth::user()->id;
        $data = $postRequest->except('_token');
        if ($isAdmin) {
            $data['approved'] = 1;
            $data['approved_by'] = $userId;
            $data['approved_at'] = now();
        }
        $post = Post::create($data);

        return $postRequest->wantsJson() ? $this->sendResponse($post, 'Post created successfully') : Redirect::route('posts.index')->with(['message' => 'Post created successfully', 'type' => 'success']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PostRequest $postRequest
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $postRequest, Post $post)
    {
        $post->title = $postRequest->title;
        $post->post = $postRequest->post;
        $post->save();

        return $postRequest->wantsJson() ? $this->sendResponse($post, 'Post updated successfully') : Redirect::route('posts.index')->with(['message' => 'Post updated successfully', 'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Post $post)
    {
        $post->delete();
        return $request->wantsJson() ? $this->sendResponse(null, 'Post deleted successfully') : Redirect::route('posts.index')->with(['message' => 'Post deleted successfully', 'type' => 'success']);
    }

    /**
     * send empty post object.
     *
     * @return \Illuminate\Http\Response
     */
    public function addNewPost()
    {
        $post = new Post();
        return view('posts.form',compact('post'));
    }

    /**
     * get individual post data from given id..
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function getPostData(Post $post)
    {
        return view('posts.form',compact('post'));
    }

    /**
     * approve post after inspection.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function approvePost(Post $post)
    {
        $post->approved = !$post->approved;
        $message = 'Post rejected successfully';
        if ($post->approved) {
            $post->approved_by = \auth()->user()->id;
            $post->approved_at = now();
            $message = 'Post approved successfully';
        }
        $post->save();

        return Redirect::route('posts.index')->with(['message' => $message, 'type' => 'success']);
    }
}
