<?php

namespace App\Http\Controllers;

use App\Models\UserFollower;
use App\Transformers\UserFollowerTransformer;
use Illuminate\Http\Request;

class UserFollowerController extends BaseController
{
    protected $entityType = UserFollower::class;
    protected $entityTransformer = UserFollowerTransformer::class;

    public function store(Request $request)
    {
        $follower = UserFollower::firstOrCreate([
            'user_following_id' => $request->user_following_id,
            'user_id' => auth()->user()->id
        ]);

        return $this->itemResponse($follower);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $follower = UserFollower::where([
            'user_following_id' => $id,
            'user_id' => auth()->user()->id
        ])->first();

        if($follower)
            $follower->delete();

            return response()->json([], 200);
    }
}
