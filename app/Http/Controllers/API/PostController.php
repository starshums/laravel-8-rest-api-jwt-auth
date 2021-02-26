<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Dotenv\Exception\ValidationException;
use Illuminate\Auth\AuthenticationException;

class PostController extends Controller {

    protected $user;

    public function __construct() {
        $this->middleware('auth:api');
        $this->user = auth()->guard()->user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $posts = $this->user->posts()->get(['id', 'title', 'body', 'author']);
        return response()->json($posts->toArray(), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make(
            $request->all(), [
                'title'    => 'required|string',
                'body' => 'required|string',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $post = new Post;
        $post->title = $request->title;
        $post->body = $request->body;

        if ($this->user->posts()->save($post)) {
            return response()->json([
                'post' => $post,
                201
            ]);
        } else {
            return response()->json([
                'message' => 'Oops, the post could not be saved.',
                400
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post) {
        return $post;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post) {
        if($post->poster->id == auth()->user()->id) {
            $post->update($request->all());
            return response()->json($post, 200);
        } else {
            throw new AuthenticationException();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post) {
        if($post->poster->id == auth()->user()->id) {
            $post->delete();
            return response()->json(null, 204);
        } else {
            throw new AuthenticationException();
        }
    }
}
