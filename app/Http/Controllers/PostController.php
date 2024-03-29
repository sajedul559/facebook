<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $request->validate([
           'status'=> 'string|max:250',
           'image'=> 'mimes:jpg,jpeg,png,svg,gif|max:3000',
        ]);
        

        if($request->hasFile('image')){
            $file= $request->file('image');
            $imageFinal= processImage($file);
        }

       $post =  Post::insert([
           'status'=> $request->get('status') ?? '',
           'photo'=> $request->hasFile('image') ? $imageFinal : '',
           'likes'=> json_encode(array()),
           'shares'=> json_encode(array()),
           'user_id'=> Auth::user()->id,
        ]);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }

    public function updateLikes(Request $request){
       
        $postId= $request->get('post_id') ?? '';
        
        if($postId){
            $post= Post::find($postId);
            $likes= $post->likes;
            $likes_array= json_decode($likes, true);

            if(in_array(auth()->user()->id, $likes_array)){
                $likes_array= array_diff($likes_array, [auth()->user()->id]);
                $post->likes= json_encode($likes_array);
                $post->save();

                return json_encode([
                   'success'=> true,
                   'result'=> -1
                ]);
            } else{
                array_push($likes_array, auth()->user()->id);
                $post->likes= json_encode($likes_array);
                $post->save();

                return json_encode([
                    'success'=> true,
                    'result'=> 1
                ]);
            }
        } else{
            return json_encode([
                'success'=> false
            ]);
        }
    }
    public function saveComment(Request $request){
      $comment = $request->get('comment')??'';
      $postId = $request->get('post_id')??''; 
      $data = new Comment();
      $data->user_id = auth()->user()->id;
      $data->post_id = $postId;
      $data->comment = $comment;
      $data->save();
      Post::find($postId)->increment('comments');
      return back();

    }
}