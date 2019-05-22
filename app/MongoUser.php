<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MongoUser extends Eloquent
{
    //

    protected $collection = 'users_collection';

    protected $fillable = [
        'Full_name', 'DOB', 'Gender',
    ];
}
