<template lang="pug">
q-layout(view="hHh lpr fFf")
  q-page-container.bg-grey-1
    q-page.flex.items-center(:padding="$q.platform.is.desktop" style="max-width: 1200px; margin: auto")
      q-card.shadow-1.full-width(:flat="$q.platform.is.mobile")
        q-card-section.q-pa-lg
          .q-gutter-md
            .flex.justify-between
              .text-h4.text-teal 電子排隊系統
              q-btn(
                @click="$q.fullscreen.toggle()"
                :icon="$q.fullscreen.isActive ? 'fullscreen_exit' : 'fullscreen'"
                round flat color="blue" v-if="$q.platform.is.desktop"
              )
            .flex
              .text-h6.text-grey-7.q-mr-lg 店舖名稱
              .text-h6.text-indigo-6.text-weight-bold {{ shop.name }}
            .row
              .col-6.flex.justify-center.items-center
                .q-px-xl.q-py-lg.bg-orange-1.text-center.self-start
                  .text-h5.text-grey-7 最新叫號
                  .text-h1.text-orange-8.text-weight-bold {{ lastTicketDisplay }}

              .col-6.flex.column.items-center
                img(:src="qr")
                .text-h6.text-pink 請以手機掃瞄QR CODE 取票
                .text-body2.text-grey-7 或手動輸入網址 <span class="text-weight-bold text-grey-8">{{ short }}</span>

        q-card-section.q-pt-none.q-pb-lg.q-px-lg
          .row.q-col-gutter-lg.text-subtitle1
            .col-3.text-center
              .q-pa-lg.bg-red-1
                .text-h5.text-weight-bold.text-red-8.q-mb-lg A 1 ~ 2人
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新入座票號
                .text-h3.text-grey-9 {{ showTicketAvailable(queueData, 'A') }}
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新獲取票號
                .text-h3.text-grey-9 {{ showTicketWaiting(queueData, 'A') }}

            .col-3.text-center
              .q-pa-lg.bg-green-1
                .text-h5.text-weight-bold.text-green-8.q-mb-lg B 3 ~ 4人
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新入座票號
                .text-h3.text-grey-9 {{ showTicketAvailable(queueData, 'B') }}
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新獲取票號
                .text-h3.text-grey-9 {{ showTicketWaiting(queueData, 'B') }}

            .col-3.text-center
              .q-pa-lg.bg-blue-1
                .text-h5.text-weight-bold.text-blue-8.q-mb-lg C 5 ~ 6人
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新入座票號
                .text-h3.text-grey-9 {{ showTicketAvailable(queueData, 'C') }}
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新獲取票號
                .text-h3.text-grey-9 {{ showTicketWaiting(queueData, 'C') }}

            .col-3.text-center
              .q-pa-lg.bg-yellow-1
                .text-h5.text-weight-bold.text-yellow-8.q-mb-lg D 7人或以上
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新入座票號
                .text-h3.text-grey-9 {{ showTicketAvailable(queueData, 'D') }}
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新獲取票號
                .text-h3.text-grey-9 {{ showTicketWaiting(queueData, 'D') }}
</template>

<script setup>
import { computed, ref } from 'vue'
import { showTicketAvailable, showTicketWaiting } from '@/useTicket'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  shop: Object,
  queues: Object,
  qr: String,
  short: String,
  lastCall: Object,
})

const queueEvent = ref(null)
const lastTicket = ref(null)

const queueData = computed(() => queueEvent.value === null ? props.queues : queueEvent.value)

const lastTicketDisplay = computed(() => {
  if (lastTicket.value === null) {
    return props.lastCall ? `${props.lastCall.ticket_type}${props.lastCall.ticket_number}` : '-'
  }
  return `${lastTicket.value.ticket_type}${lastTicket.value.ticket_number}`
})

const audio = new Audio('https://q.jamwong.me/mixkit-happy-bells-notification-937.wav')

const onEvent = evt => {
  audio.play()
  queueEvent.value = evt.queues
  lastTicket.value = evt.latest
}

window.Echo.channel(`queue.${props.shop.uuid}`)
  .listen('QueueCreated', evt => queueEvent.value = evt.queues)
  .listen('QueueUpdated', onEvent)
  .listen('QueueReset', () => {
    queueEvent.value = null
    Inertia.reload()
  })

</script>
