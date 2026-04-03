<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Notifications\Notifiable;

#[Fillable(['customer_id','topic', 'text', 'status','response'])]
class Ticket extends Model implements HasMedia
{
            use InteractsWithMedia , HasFactory;
             

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    
}
