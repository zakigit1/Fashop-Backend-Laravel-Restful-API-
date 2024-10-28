<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;


    
    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];
    // protected $fillable = ['name','email','password','phone','image','username'];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */


    protected $hidden = [
        // 'id',
        'password',
        'created_at',
        'updated_at',
        'remember_token',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    //this using when i create a token of user : is optional 
    const USER_TOKEN = "userToken";


##################################################################################################################


    public function scopeActive($query) // to show just the active slide in store 
    {
        return $query->where('status', 'active');
    }       
   
/*                                                  End Local Scopes                                  */

/*                                                 Begin GET                                          */
    public function getImageAttribute($value)
    {
        return ($value !== NULL) ? asset( 'storage/uploads/images/users/'.$value) : " ";
    }
/*                                                 End GET                                            */







}
