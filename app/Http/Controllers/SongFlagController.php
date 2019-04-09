<?php

namespace App\Http\Controllers;

use App\Models\SongFlag;
use App\Transformers\SongFlagTransformer;
use Illuminate\Http\Request;

class SongFlagController extends BaseController
{
    protected $entityType = SongFlag::class;
    protected $entityTransformer = SongFlagTransformer::class;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_flag = SongFlag::firstOrCreate(
            ['song_id' => $request->song_id], 
            ['user_id' => auth()->user()->id]);

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
        $user_flag = SongFlag::where(
            ['song_id' => $id], 
            ['user_id' => auth()->user()->id])->first();
        
        if($user_flag)
            $user_flag->delete();

            return response()->json([], 200);
    }

}
