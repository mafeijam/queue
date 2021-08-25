<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public static function toEvent($shop)
    {
        return static::where('shop_id', $shop)->get()->convertQueuesCollection();
    }

    public static function getLatest($request)
    {
        return (static::where('shop_id', $request->shop)
            ->where('ticket_type', $request->type)
            ->orderBy('ticket_number', 'desc')
            ->first()->ticket_number ?? 0) + 1;
    }

    public static function getLastAvailable($request)
    {
        return static::where('shop_id', $request->shop)
            ->where('ticket_type', $request->type)
            ->where('available', false)
            ->whereNull('cancelled_at')
            ->orderBy('ticket_number')->first();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
