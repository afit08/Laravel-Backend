<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CommentsController extends Controller
{
    public function postComments(Request $request)
    {
        try {
            $role = auth()->user()->roles == "user";
            if ($role === true) {
                $newsId = $request->id;
                $news = Cache::remember("news_$newsId", 60, function () use ($newsId) {
                    return DB::table('news')->where('news_id', $newsId)->get();
                });

                $result = DB::table('comments')->insert([
                    "com_news_id" => $news[0]->news_id,
                    "com_user_id" => auth()->user()->id,
                    "com_desc" => $request->com_desc
                ]);

                // Clear the cache when inserting a new comment.
                Cache::forget("news_$newsId");

                return response()->json([
                    "message" => "Create Comments",
                    "data" => $result
                ], 200);
            } else {
                return response()->json([
                    "message" => "Unauthorized",
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage()
            ], 500);
        }
    }
}
