<?php

namespace Mralgorithm\JobRunner\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BackgroundJobs extends Model
{
    use HasFactory;
    protected $table = 'background_jobs';

    protected $fillable = [
        'priority',
        'payload',
        'attempts',
        'available_at',
        'status'
    ];

    protected $appends = ['params','class','method','max_retires'];

    protected function params(): Attribute
    {
        $payload_array = json_decode($this->payload,1);

        $params = [];
        if(isset($payload_array['params'])){
            $params = $payload_array['params'];
        }
        return new Attribute(
            get: fn () => $params,
        );
    }

    protected function class(): Attribute
    {
        $payload_array = json_decode($this->payload,1);

        return new Attribute(
            get: fn () => $payload_array['class'],
        );
    }

    protected function method(): Attribute
    {
        $payload_array = json_decode($this->payload,1);

        return new Attribute(
            get: fn () => $payload_array['method'],
        );
    }

    protected function maxRetires(): Attribute
    {
        $payload_array = json_decode($this->payload,1);

        return new Attribute(
            get: fn () => $payload_array['max_retires'],
        );
    }
}