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

          template(v-if="isMineBool && queue.cancelled_at === null")
            .bg-purple-1.q-pa-md.text-subtitle1.text-purple-8
              q-icon.q-mr-md(name="announcement" size="2rem")
              span 到你啦！

          .bg-pink-1.q-pa-md.text-body2.text-pink-6(v-if="queue.cancelled_at")
            q-icon.q-mr-md(name="announcement" size="2rem")
            span 你已於 {{ queue.cancelled_at }} 取消排隊

          .flex.justify-between.items-baseline
            .text-h6.text-grey-7 你的票號
            .text-body2.text-grey-7 取票時間 {{ queue.created_at }}
          .text-h2.flex.justify-center.q-pa-lg.text-weight-bold(:class="colors[queue.ticket_type]") {{ queue.ticket_type }}{{ queue.ticket_number }}
          .flex.justify-between.items-baseline
            .text-h6.text-grey-7 最新叫號
            .text-body2.text-grey-7 叫號時間 {{ lastCallTime(queueData, queue.ticket_type) }}
          .text-h2.flex.justify-center.q-pa-lg.bg-orange-1.text-orange-8.text-weight-bold {{ showTicketAvailable(queueData, queue.ticket_type)  }}
          .text-center(v-if="canCancel")
            q-btn(label="取消排隊" color="pink" size="lg" icon="close" @click="cancel" :loading="loading")
</template>

<script setup>
import { computed, ref } from 'vue'
import { showTicketAvailable, isMine, isBefore, lastCallTime } from '@/useTicket'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  queue: Object,
  queues: Object,
})

const loading = ref(false)
const queueEvent = ref(null)
const queueData = computed(() => queueEvent.value === null ? props.queues : queueEvent.value)
const isMineBool = computed(() => isMine(queueData.value, props.queue.ticket_type, props.queue.ticket_number))
const canCancel = computed(() => props.queue.cancelled_at === null && isBefore(queueData.value, props.queue.ticket_type, props.queue.ticket_number))

const colors = {
  A: 'bg-red-1 text-red-8',
  B: 'bg-green-1 text-green-8',
  C: 'bg-blue-1 text-blue-8',
  D: 'bg-yellow-1 text-yellow-8'
}

const cancel = () => {
  Inertia.delete(`/cancel_ticket/${props.queue.uuid}`, {
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false
  })
}

const audio = new Audio('https://q.jamwong.me/mixkit-happy-bells-notification-937.wav')

const onEvent = evt => {
  if (props.queue.cancelled_at) {
    return
  }

  queueEvent.value = evt.queues
  isMineBool.value && audio.play()
}

window.Echo.channel(`queue.${props.queue.shop.uuid}`)
  .listen('QueueUpdated', onEvent)

</script>
