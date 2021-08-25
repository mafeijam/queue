<?php

namespace App\Http\Controllers;

use App\Events\QueueReset;
use App\Events\QueueUpdated;
use App\Mail\ShopCreated;
use App\Models\Queue;
use App\Models\Shop;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function store(Request $request)
    {
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
            Log::channel('debug')->debug('shop error', [$e]);
            return back()->with('message', '系統發生錯誤，請稍後再嘗試！');
        }

        Log::channel('debug')->info('shop created', [$passcode, $shop->name, $shop->getBackendLink()]);

        return redirect()->route('queue', [
            'uuid' => $uuid
        ]);
    }

    public function frontend($uuid)
    {
        $shop = Shop::with('queues')->where('uuid', $uuid)->firstOrFail();
        $queues = $shop->convertQueues();
        [$qr, $short] = $shop->getQRCodeAndLink();
        $lastCall = $shop->queues->where('available', 1)->sortByDesc('updated_at')->first();
        return inertia('Queue', compact('shop', 'queues', 'qr', 'short', 'lastCall'));
    }

    public function backend(Request $request, $uuid)
    {
        try {
            $uuid = decrypt($uuid);
        } catch (DecryptException $e) {
            Log::channel('debug', 'decrypt backend error');
            return redirect('/');
        }

        $shop = Shop::with('queues')->where('uuid', $uuid)->firstOrFail();

        if ($this->notLoggedIn($request, $shop->id)) {
            return inertia('Passcode', compact('shop'));
        }

        $queues = $shop->convertQueues();
        return inertia('Shop', compact('shop', 'queues'));
    }

    public function login(Request $request)
    {
        $shop = Shop::find($request->shop);

        $request->validate([
            'passcode' => 'required',
        ], [
            'passcode.required' => '請輸入密碼'
        ]);

        if (Hash::check($request->passcode, $shop->passcode)) {
            $request->session()->put('passcode.'.$shop->id, true);
            return back();
        }

        return back()->with('message', '密碼錯誤');
    }

    public function next(Request $request)
    {
        if ($this->notLoggedIn($request, $request->shop)) {
            return inertia('Passcode', compact('shop'));
        }

        $latest = Queue::getLastAvailable($request);

        $latest->update([
            'available' => true
        ]);

        $queues = Queue::toEvent($request->shop);

        broadcast(new QueueUpdated($request->uuid, $queues, $latest));

        return back();
    }

    public function reset(Request $request, $uuid)
    {
        $shop = Shop::where('uuid', $uuid)->first();

        if ($this->notLoggedIn($request, $shop->id)) {
            return inertia('Passcode', compact('shop'));
        }

        $shop->queues()->delete();

        broadcast(new QueueReset($shop->uuid));

        return back();
    }

    protected function notLoggedIn($request, $shop)
    {
        return $request->session()->get("passcode.$shop", false) === false;
    }
}
