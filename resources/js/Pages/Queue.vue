<template lang="pug">
q-layout(view="hHh lpr fFf")
  q-page-container.bg-grey-1
    q-page.flex.items-center(:padding="$q.platform.is.desktop" style="max-width: 1200px; margin: auto")
      q-card.shadow-1.full-width(:flat="$q.platform.is.mobile")
        q-card-section.q-pa-lg
          .flex.justify-between.q-mb-md
            .text-h4.text-teal 電子排隊系統
            q-btn(
              @click="$q.fullscreen.toggle()"
              :icon="$q.fullscreen.isActive ? 'fullscreen_exit' : 'fullscreen'"
              round flat color="blue" v-if="$q.platform.is.desktop"
            )
          .flex.q-mb-md
            .text-h6.text-grey-7.q-mr-lg 店舖名稱
            .text-h6.text-indigo-6.text-weight-bold {{ shop.name }}

          .row.q-col-gutter-lg
            .col-6
              .full-height.bg-orange-1.flex.column.justify-center.items-center
                .text-h5.text-grey-7.q-mb-md 最新叫號
                .text-h1.text-orange-8.text-weight-bold {{ lastTicketDisplay }}

            .col-6.flex.column.items-center
              img.q-mb-md(:src="qr")
              .text-h6.text-pink 請以手機掃瞄QR CODE 取票
              .text-body2.text-grey-7 或手動輸入網址 <span class="text-weight-bold text-grey-8">{{ short }}</span>

        q-card-section.q-pt-none.q-pb-lg.q-px-lg
          .row.q-col-gutter-lg.text-subtitle1
            TicketCard(color="red" title="A 1 ~ 2人" type="A" :queueData="queueData")
            TicketCard(color="green" title="B 3 ~ 4人" type="B" :queueData="queueData")
            TicketCard(color="blue" title="C 5 ~ 6人" type="C" :queueData="queueData")
            TicketCard(color="yellow" title="D 7人或以上" type="D" :queueData="queueData")
</template>

<script setup>
import { computed, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import TicketCard from '@/Components/TicketCard.vue'

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
  .listen('QueueCancelled', evt => queueEvent.value = evt.queues)
  .listen('QueueUpdated', onEvent)
  .listen('QueueReset', () => {
    queueEvent.value = null
    Inertia.reload()
  })

</script>
