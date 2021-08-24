<template lang="pug">
q-layout(view="hHh lpr fFf")
  q-page-container.bg-grey-1
    q-card.window-height(style="max-width: 800px; margin: auto" flat)
      q-card-section.q-pa-md
        .q-gutter-md
          .text-h4.text-teal 電子排隊系統
          .flex
            .text-h5.text-grey-7.q-mr-lg 店舖名稱
            .text-h5.text-indigo-7 {{ queue.shop.name }}

          template(v-if="isMineBool")
            .bg-purple-1.q-pa-md.text-subtitle1.text-purple-8
              q-icon.q-mr-md(name="announcement" size="2rem")
              span 到你啦！
          .text-h6.text-grey-7 你的票號
          .text-h2.flex.justify-center.q-pa-lg.text-weight-bold(:class="colors[queue.ticket_type]") {{ queue.ticket_type }}{{ queue.ticket_number }}
          .text-body2.text-grey-7 取票時間 {{ queue.created_at }}
          .text-h6.text-grey-7 最新叫號
          .text-h2.flex.justify-center.q-pa-lg.bg-orange-1.text-orange-8.text-weight-bold {{ showTicketAvailable(queueData, queue.ticket_type)  }}
          .text-body2.text-grey-7 叫號時間 {{ lastCallTime(queueData, queue.ticket_type) }}
</template>

<script setup>
import { computed, ref } from 'vue'
import { showTicketAvailable, isMine, lastCallTime } from '@/useTicket'

const props = defineProps({
  queue: Object,
  queues: Object,
})

const queueEvent = ref(null)
const queueData = computed(() => queueEvent.value === null ? props.queues : queueEvent.value)
const isMineBool = computed(() => isMine(queueData.value, props.queue.ticket_type, props.queue.ticket_number))

const colors = {
  A: 'bg-red-1 text-red-8',
  B: 'bg-green-1 text-green-8',
  C: 'bg-blue-1 text-blue-8',
  D: 'bg-yellow-1 text-yellow-8'
}

const audio = new Audio('https://q.jamwong.me/mixkit-happy-bells-notification-937.wav')

const onEvent = evt => {
  isMineBool && audio.play()
  queueEvent.value = evt.queues
}

window.Echo.channel(`queue.${props.queue.shop.uuid}`)
  .listen('QueueUpdated', onEvent)

</script>
