<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PostController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $paginate = Auth::user()->paginate;
        $isAdmin = Auth::user()->admin;
        $posts = Post::orderBy('created_at', 'desc')->paginate($paginate);
        return view('posts.index', compact('posts', 'isAdmin'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Post $post
     * @return array[]
     */
    public function show(Post $post)
    {
        return [
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'body' => $post->post,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at
            ]
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostRequest $postRequest
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(PostRequest $postRequest)
    {
        $user = Auth::user();
        $isAdmin = $user->admin;
        $userId = $user->id;
        $isBlocked = $user->blocked;

        if (!$isBlocked) {
            $data = $postRequest->except('_token');
            if ($isAdmin) {
                $data['approved'] = 1;
                $data['approved_by'] = $userId;
                $data['approved_at'] = now();
            }
            $post = Post::create($data);

            return $postRequest->wantsJson() ? $this->sendResponse($post, 'Post created successfully') : Redirect::route('posts.index')->with(['message' => 'Post created successfully', 'type' => 'success']);
        } else {
            return $postRequest->wantsJson() ? $this->sendError(null, 'Your account have been blocked.') : Redirect::route('posts.index')->with(['message' => 'Your account have been blocked.', 'type' => 'error']);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param PostRequest $postRequest
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(PostRequest $postRequest, Post $post)
    {
        $user = Auth::user();
        $isAdmin = $user->admin;
        $userId = $user->id;
        $isBlocked = $user->blocked;

        if (!$isBlocked) {

            // If user is not an admin change approve status 0
            if (!$isAdmin) {
                $post->approved = 0;
                $post->approved_by = null;
                $post->approved_at = null;
            }

            $post->title = $postRequest->title;
            $post->post = $postRequest->post;
            $post->save();

            return $postRequest->wantsJson() ? $this->sendResponse($post, 'Post updated successfully') : Redirect::route('posts.index')->with(['message' => 'Post updated successfully', 'type' => 'success']);
        } else {
            return $postRequest->wantsJson() ? $this->sendError(null, 'Your account have been blocked.') : Redirect::route('posts.index')->with(['message' => 'Your account have been blocked.', 'type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Post $post)
    {
        $post->delete();
        return $request->wantsJson() ? $this->sendResponse(null, 'Post deleted successfully') : Redirect::route('posts.index')->with(['message' => 'Post deleted successfully', 'type' => 'success']);
    }

    /**
     * send empty post object.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addNewPost()
    {
        $post = new Post();
        return view('posts.form', compact('post'));
    }

    /**
     * get individual post data from given id..
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getPostData(Post $post)
    {
        return view('posts.form', compact('post'));
    }

    /**
     * approve post after inspection.
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approvePost(Post $post)
    {
        $isAdmin = \auth()->user()->admin;
        if ($isAdmin) {
            $post->approved = !$post->approved;
            $message = 'Post rejected successfully';
            if ($post->approved) {
                $post->approved_by = \auth()->user()->id;
                $post->approved_at = now();
                $message = 'Post approved successfully';
            }
            $post->save();

            return Redirect::route('posts.index')->with(['message' => $message, 'type' => 'success']);
        } else {
            return Redirect::route('posts.index')->with(['message' => 'You are not authorized to approve posts.', 'type' => 'error']);
        }
    }

    /**
     * return all approved posts.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function posts(Request $request)
    {
        $paginate = Auth::check() ? Auth::user()->paginate : 10;
        $posts = Post::whereApproved(1)->whereHas('user', function (Builder $query) {
            $query->where('blocked', '=', 0);
        })->orderBy('created_at','desc')->paginate($paginate);
        return PostResource::collection($posts);
    }

    /**
     * Search post with given keyword.
     *
     * @param $keyword
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search($keyword)
    {
        $posts = Post::whereApproved(1)
            ->where("title", "like", "%$keyword%")
            ->orWhere("post", "like", "%$keyword%")
            ->orWhereHas('user', function (Builder $query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            })->get();
        return PostResource::collection($posts);
    }

    /**
     * return all posts of logged in user.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function userPosts()
    {
        $userId = \auth()->user()->id;
        $user = User::find($userId);
        $posts = $user->posts()->get();
        return PostResource::collection($posts);
    }
}
