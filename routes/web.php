<?php

use App\Events\QueueCreated;
use App\Events\QueueReset;
use App\Events\QueueUpdated;
use App\Mail\ShopCreated;
use App\Models\Queue;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

Route::inertia('/', 'Index');

Route::post('create_shop', function (Request $request) {
    $data = $request->validate([
        'name' => 'required',
        'email' => 'required|email'
    ], [
        'name.required' => '請輸入店舖名稱',
        'email.required' => '請輸入電郵地址',
        'email.email' => '請輸入正確的電郵格式'
    ]);

    $uuid = Str::uuid();
    $passcode = strtolower(Str::random(6));

    do {
        $shortLink = strtolower(Str::random(4));
    } while (Shop::where('short_link', $shortLink)->exists());

    DB::beginTransaction();

    try {
        $shop = Shop::create(array_merge($data, [
            'uuid' => $uuid,
            'passcode' => bcrypt($passcode),
            'short_link' => $shortLink
        ]));

        Mail::to($data['email'])->send(new ShopCreated($shop, $passcode));
        DB::commit();
    } catch (Exception $e) {
        return back()->with('message', '系統發生錯誤，請稍後再嘗試！');
    }

    Log::channel('debug')->info('shop created', [$passcode, $shop]);

    return redirect()->route('queue', [
        'uuid' => $uuid
    ]);
});


Route::get('queue/{uuid}', function ($uuid) {
    $shop = Shop::with('queues')->where('uuid', $uuid)->firstOrFail();
    $queues = $shop->convertQueues();
    [$qr, $short] = $shop->getQRCodeAndLink();
    $lastCall = $shop->queues->where('available', 1)->sortByDesc('updated_at')->first();
    return inertia('Queue', compact('shop', 'queues', 'qr', 'short', 'lastCall'));
})->name('queue');


Route::get('shop/{uuid}', function (Request $request, $uuid) {
    $shop = Shop::with('queues')->where('uuid', decrypt($uuid))->firstOrFail();

    if ($request->session()->get('passcode.'.$shop->id, false) === false) {
        return inertia('Passcode', compact('shop'));
    }

    $queues = $shop->convertQueues();
    return inertia('Shop', compact('shop', 'queues'));
})->name('shop');

Route::post('passcode', function (Request $request) {
    $shop = Shop::find($request->shop);

    $request->validate([
        'passcode' => 'required',
    ], [
        'passcode.required' => '請輸入軛密碼'
    ]);

    if (Hash::check($request->passcode, $shop->passcode)) {
        $request->session()->put('passcode.'.$shop->id, true);
        return back();
    }

    return back()->with('message', '密碼錯誤');
});

Route::get('c/{link}', function (Request $request, $link) {
    $shop = Shop::where('short_link', $link)->firstOrFail();

    $ticket = [
        'exists' => false,
        'link' => null
    ];

    $taken = collect($request->session()->get('taken'))
        ->where('shop', $shop->uuid)
        ->sortByDesc('date')->first();

    if ($taken && Queue::where('uuid', $taken['uuid'])->first()) {
        $ticket = [
            'exists' => true,
            'link' => $taken['route']
        ];
    }

    return inertia('Client', compact('shop', 'ticket'));
})->name('client');

Route::post('get_ticket', function (Request $request) {
    $taken = collect($request->session()->get('taken'))
        ->where('shop', $request->uuid)
        ->sortByDesc('date')->first();

    if ($taken && Queue::where('uuid', $taken['uuid'])->first()) {
        return redirect()->route('ticket', [
            'uuid' => encrypt($taken['uuid'])
        ])->with('message', '你已於較早前取票');
    }

    $uuid = Str::uuid();

    Queue::create([
        'shop_id' => $request->shop,
        'uuid' => $uuid,
        'ticket_type' => $request->type,
        'ticket_number' => Queue::getLatest($request)
    ]);

    $queues = Queue::toEvent($request->shop);

    broadcast(new QueueCreated($request->uuid, $queues));

    $params = ['uuid' => encrypt($uuid)];

    $request->session()->push('taken', [
        'shop' => $request->uuid,
        'uuid' => $uuid,
        'route' => route('ticket', $params),
        'date' => now()
    ]);

    return redirect()->route('ticket', $params);
});

Route::get('ticket/{uuid}', function ($uuid) {
    $queue = Queue::with('shop.queues')->where('uuid', decrypt($uuid))->firstOrFail();
    $queues = $queue->shop->convertQueues();
    return inertia('Ticket', compact('queue', 'queues'));
})->name('ticket');

Route::post('call_next', function (Request $request) {
    $latest = Queue::where('shop_id', $request->shop)
        ->where('ticket_type', $request->type)
        ->where('available', false)
        ->orderBy('ticket_number')->first();

    $latest->update([
        'available' => true
    ]);

    $queues = Queue::toEvent($request->shop);

    broadcast(new QueueUpdated($request->uuid, $queues, $latest));

    return back();
});

Route::delete('reset/{uuid}', function ($uuid) {
    $shop = Shop::where('uuid', $uuid)->first();
    $shop->queues()->delete();

    broadcast(new QueueReset($shop->uuid));

    return back();
});

Route::get('mail', function () {
    $shop = Shop::first();
    $passcode = strtolower(Str::random(6));
    return new ShopCreated($shop, $passcode);
});
