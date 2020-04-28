<?php

namespace App\Http\Controllers;

use App\Models\UserFlag;
use App\Transformers\UserFlagTransformer;
use Illuminate\Http\Request;

class UserFlagController extends BaseController
{


    protected $entityType = UserFlag::class;
    protected $entityTransformer = UserFlagTransformer::class;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_flag = UserFlag::firstOrCreate([
            'flag_user_id' => $request->user_id,
            'user_id' => auth()->user()->id
        ]);

        return $this->itemResponse($user_flag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_flag = UserFlag::where([
            'flag_user_id' => $id,
            'user_id' => auth()->user()->id
        ])->first();

        if($user_flag)
            $user_flag->delete();

            return response()->json([], 200);
    }

}
