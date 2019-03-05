
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackComment extends Model
{
    use SoftDeletes;

    public function track()
    {
        return  $this->belongsTo(Track::class);
    }

}
