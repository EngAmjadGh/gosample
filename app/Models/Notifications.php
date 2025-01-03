<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = 'notifications';
    protected $fillable = ['id', 'read_at'];
    protected $casts = [
        'data' => 'array',
        'id' => 'string'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function client(){
        return $this->belongsTo(Client::class, "billing_client", 'id');
    }
    public function driver(){
        return $this->belongsTo(Driver::class);
    }
    public function fromLocation(){
        return $this->belongsTo(Location::class, "from_location", "id");
    }
    public function toLocation(){
        return $this->belongsTo(Location::class, "to_location", "id");
    }

    public function from_location()
    {
        return $this->belongsTo(Location::class, 'from_location','id');
    }

    public function to_location()
    {
        return $this->belongsTo(Location::class, 'to_location','id');
    }
    public function billingClient()
    {
        return $this->belongsTo(Client::class, 'billing_client','id');
    }
    public function billing_client()
    {
        return $this->belongsTo(Client::class, 'billing_client','id');
    }
}
