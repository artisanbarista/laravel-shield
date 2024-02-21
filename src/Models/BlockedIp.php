<?php

namespace Webdevartisan\LaravelBlocker\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{

    use HasFactory;

    public $timestamps = false;

    protected $table = 'ai_blocked_ips';

    protected $fillable = [
        'ip',
        'expires_at',
    ];

    public function hasExpired()
    {
        return $this->expires_at < date('Y-m-d H:i:s');
    }

    protected static function newFactory()
    {
        return \Webdevartisan\LaravelBlocker\Database\Factories\BlockedIpFactory::new();
    }
}
