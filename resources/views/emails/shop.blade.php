@component('mail::message')
# *{{ $shop->name }}*，歡迎使用本服務

@component('mail::panel')
你的後台密碼 **{{ $passcode }}**
@endcomponent

@component('mail::button', ['url' => route('shop', ['uuid' => encrypt($shop->uuid)]), 'color' => 'primary'])
點擊登入後台
@endcomponent

@component('mail::button', ['url' => route('queue', ['uuid' => $shop->uuid]), 'color' => 'green'])
點擊進入票號頁面
@endcomponent

### 如果點擊按鈕無法進入，可手動COPY 以下連結

(後台頁面)

**{{ wordwrap(route('shop', ['uuid' => encrypt($shop->uuid)]), 45, "\n", true) }}**

(輪候頁面)

**{{ wordwrap(route('queue', ['uuid' => $shop->uuid]), 45, "\n", true) }}**
@endcomponent
