<?php

namespace App\Http\Controllers;

use App\Events\QueueCreated;
use App\Models\Queue;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index(Request $request, $link)
    {
        $shop = Shop::where('short_link', $link)->firstOrFail();

        $ticket = [
            'exists' => false,
            'link' => null
        ];

        [$taken, $queue] = $this->checkTaken($request, $shop->uuid);

        if ($taken && $queue) {
            $ticket = [
                'exists' => true,
                'link' => $taken['route']
            ];
        }

        return inertia('Client', compact('shop', 'ticket'));
    }

    public function store(Request $request)
    {
        [$taken, $queue] = $this->checkTaken($request);

        if ($taken && $queue) {
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
    }

    public function show($uuid)
    {
        $queue = Queue::with('shop.queues')->where('uuid', decrypt($uuid))->firstOrFail();
        $queues = $queue->shop->convertQueues();
        return inertia('Ticket', compact('queue', 'queues'));
    }

    protected function checkTaken($request, $uuid = null)
    {
        $taken = collect($request->session()->get('taken'))
            ->where('shop', $uuid ?? $request->uuid)
            ->sortByDesc('date')->first();

        $queue = $taken ? Queue::where('uuid', $taken['uuid'])->exists() : false;

        return [$taken, $queue];
    }
}
