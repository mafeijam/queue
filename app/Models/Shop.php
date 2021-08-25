<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\QRCode;

class Shop extends Model
{
    use HasFactory;

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function convertQueues()
    {
        return $this->queues->convertQueuesCollection();
    }

    public function getQRCodeAndLink()
    {
        $short = route('client', ['link' => $this->short_link]);

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'quietzoneSize' => 2,
            'scale' => 5,
        ]);

        $qr = (new QRCode($options))->render($short);

        return [$qr, $short];
    }

    public function getFrontendLink()
    {
        return route('queue', ['uuid' => $this->uuid]);
    }

    public function getBackendLink()
    {
        return route('shop', ['uuid' => encrypt($this->uuid)]);
    }
}
