<?php

namespace App\Http\Controllers\API;

use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return response()->json([
            'result' => 'success',
            'message' => 'greetings from comments'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $noConent = !$request->has('content');
        $noPostId = !$request->has('post_id');
        if ($noConent || $noPostId){
            return response()->json([
                'result' => 'error',
                'message' => 'Missing required parameter' . ($noConent ? ' Content' : '') . ($noPostId ? ' Post_id' : '')
            ]);
        }
        $content = $request->get('content');
        $post_id = $request->get('post_id');
        $comment = new Comment();
        $comment->post_id = $post_id;
        $comment->content = $content;
        $comment->user_id = Auth::user()->id;
        $comment->save();
        return response()->json([
            'result' => 'success',
            'comment' => $comment
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request){
        if (!$request->has('comment')){
            return response()->json([
                'result' => 'error',
                'message' => 'You should set the comment id to show with a parameter named comment'
            ]);
        }
        $comment = Comment::find($request->get('comment'));
        if (is_null($comment)){
            return response()->json([
                'result' => 'error',
                'message' => 'Selected comment is invalid'
            ]);
        }

        return response()->json([
            'result' => 'success',
            'comment' => $comment
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        if (!$request->has('comment')){
            return response()->json([
                'result' => 'error',
                'message' => 'You should set the comment id to update with a parameter named category'
            ]);
        }
        $comment = Comment::find($request->get('comment'));
        if (is_null($comment)){
            return response()->json([
                'result' => 'error',
                'message' => 'Selected comment is invalid'
            ]);
        }


        $noConent = !$request->has('content');
        if ($noConent){
            return response()->json([
                'result' => 'error',
                'message' => 'Missing required parameter' . ($noConent ? ' Content' : '')
            ]);
        }

        $content = $request->get('content');
        $comment->content = $content;
        $comment->save();


        return response()->json([
            'result' => 'success',
            'comment' => $comment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request){
        if (!$request->has('comment')){
            return response()->json([
                'result' => 'error',
                'message' => 'You should set the comment id to delete with a parameter named comment'
            ]);
        }
        $comment = Comment::find($request->get('comment'));
        if (is_null($comment)){
            return response()->json([
                'result' => 'error',
                'message' => 'Selected comment is invalid'
            ]);
        }

        $comment->delete();
        return response()->json([
            'result' => 'success',
            'message' => 'Comment deleted successfully'
        ]);
    }
}
