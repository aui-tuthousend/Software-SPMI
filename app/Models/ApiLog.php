<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'method', 'url', 
        'request_headers', 'request_body',
        'status_code', 'response_body',
        'ip_address', 'user_agent', 'response_time'
    ];
    
    protected $casts = [
        'request_headers' => 'array',
        'request_body' => 'array',
        'response_body' => 'array',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
