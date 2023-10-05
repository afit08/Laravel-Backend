<?php

namespace App\Http\Controllers;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function index()
    {
        try {
            $perPage = request('per_page', 10);
            $result = DB::table('news')->paginate($perPage);

            $role = auth()->user()->roles == "admin";

            if ($role === true) {
                return response()->json([
                    "message" => "List News",
                    "data" => $result
                ], 200);
            } else {
                return response()->json([
                    "message" => "Unauthorized",
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th
            ], 500);
        }
    }


    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $role = auth()->user()->roles == "admin";

            if ($role === true) {
                $Now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                if ($request->hasFile('news_image')) {
                    $image = $request->file('news_image');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);

                    // Save the image path in the database
                    $imagePath = 'images/' . $imageName;
                } else {
                    $imagePath = null;
                }

                $result = DB::table('news')->insertGetId([
                    "news_name" => $request->news_name,
                    "news_desc" => $request->news_desc,
                    "news_image" => $imagePath,
                    "news_user_id" => auth()->user()->id,
                    "created_at" => $Now->format('Y-m-d H:i:s')
                ]);

                DB::table('news_logs')->insert([
                    "news_id" => $result,
                    "action" => "Create News",
                    "created_at" => $Now->format('Y-m-d H:i:s')
                ]);

                DB::commit();

                return response()->json([
                    "message" => "Create News",
                    "data" => $result
                ]);
            } else {
                DB::rollback();
                return response()->json([
                    "message" => "Unauthorized",
                ], 401);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "message" => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $role = auth()->user()->roles == "admin";
            if ($role === true) {
                $Now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                if ($request->hasFile('news_image')) {
                    $image = $request->file('news_image');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $imageName);

                    // Save the image path in the database
                    $imagePath = 'images/' . $imageName;
                } else {
                    $imagePath = null;
                }

                $result = DB::table('news')->where('news_id', $request->id)->update([
                    "news_name" => $request->news_name,
                    "news_desc" => $request->news_desc,
                    "news_image" => $imagePath,
                    "news_user_id" => auth()->user()->id,
                    "updated_at" => $Now->format('Y-m-d H:i:s')
                ]);

                DB::table('news_logs')->insert([
                    "news_id" => $result,
                    "action" => "Update News",
                    "updated_at" => $Now->format('Y-m-d H:i:s')
                ]);

                DB::commit();

                return response()->json([
                    "message" => "Update News",
                    "data" => $result
                ]);
            } else {
                DB::rollback();
                return response()->json([
                    "message" => "Unauthorized",
                ], 401);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "message" => $th->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $role = auth()->user()->roles == "admin";

            if ($role === true) {
                $Now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                $result = DB::table('news')->where('news_id', $id)->delete();

                DB::table('news_logs')->insert([
                    "news_id" => $result,
                    "action" => "Delete News",
                    "updated_at" => $Now->format('Y-m-d H:i:s')
                ]);

                DB::commit();

                return response()->json([
                    "message" => "Delete News",
                    "data" => $result
                ], 200);
            } else {
                DB::rollback();
                return response()->json([
                    "message" => "Unauthorized",
                ], 401);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "message" => $th->getMessage()
            ], 500);
        }
    }

    public function detail($id)
    {
        try {
            if (auth()->user()->roles) {
                $result = DB::table('news as a')
                    ->leftJoin('comments as b', 'b.com_news_id', '=', 'a.news_id')
                    ->where('a.news_id', $id)
                    ->get();

                return response()->json([
                    "message" => "Detail News",
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
