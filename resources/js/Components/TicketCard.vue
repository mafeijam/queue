<template lang="pug">
.col-3.text-center
  .q-pa-lg(:class="cardColor[color][0]")
    .text-h5.text-weight-bold.q-mb-lg(:class="cardColor[color][1]") {{ title }}
    q-separator(spaced="lg")
    .text-h6.text-grey-7 最新入座票號
    .text-h3.text-grey-9 {{ showTicketAvailable(queueData, type) }}
    q-separator(spaced="lg")
    .text-h6.text-grey-7 最新獲取票號
    .text-h3.text-grey-9 {{ showTicketWaiting(queueData, type) }}
    .flex.justify-between.q-mt-lg.text-grey-7
      .flex.items-baseline
        .text-caption.q-mr-md 等候中
        .text-body1 {{ queueData[type]?.diff ?? '0' }}
      .flex.items-baseline
        .text-caption.q-mr-md 已取消
        .text-body1 {{ queueData[type]?.cancelled ?? '0' }}
</template>

<script setup>
import { showTicketAvailable, showTicketWaiting } from '@/useTicket'

defineProps({
  color: String,
  title: String,
  type: String,
  queueData: Object
})

const cardColor = {
  red: ['bg-red-1', 'text-red-8'],
  green: ['bg-green-1', 'text-green-8'],
  blue: ['bg-blue-1', 'text-blue-8'],
  yellow: ['bg-yellow-1', 'text-yellow-8'],
}
</script>
