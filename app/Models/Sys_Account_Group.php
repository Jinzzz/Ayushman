<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sys_Account_Group extends Model
{
    use HasFactory;
    protected $table = 'sys__account__groups';
    
    public function subHead()
    {
        return $this->hasMany(Mst_Account_Sub_Head::class, 'account_group_id', 'id');
    }

}
