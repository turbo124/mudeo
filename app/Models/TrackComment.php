
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackComment extends Model
{
    public function track()
    {
        $this->belongsTo(Track::class);
    }

}
