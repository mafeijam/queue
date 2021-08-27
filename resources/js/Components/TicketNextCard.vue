<template lang="pug">
.col-3.text-center
  .q-pt-sm.q-px-sm(:class="cardColor[color][0]")
    .text-right
      q-btn(
        label="查閱明細" flat icon="list" dense :color="cardColor[color][2]"
        @click="viewMore" :disable="noDetails(queueData, type)"
      )
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
        .text-body1.text-weight-bold {{ queueData[type]?.diff ?? '0' }}
      .flex.items-baseline
        .text-caption.q-mr-md 已取消
        .text-body1.text-weight-bold {{ queueData[type]?.cancelled ?? '0' }}
    q-btn.full-width.q-mt-lg(
      label="下一個" :color="noNext(queueData, type) ? 'grey-5' : 'red'"
      size="lg"
      @click="callNext"
      :disable="noNext(queueData, type)"
    )
</template>

<script setup>
import { showTicketAvailable, showTicketWaiting, showDetails, noDetails, noNext } from '@/useTicket'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  color: String,
  title: String,
  type: String,
  queueData: Object,
  shop: Object,
})

const emit = defineEmits(['selected', 'called'])

const cardColor = {
  red: ['bg-red-1', 'text-red-8', 'red-8'],
  green: ['bg-green-1', 'text-green-8', 'green-8'],
  blue: ['bg-blue-1', 'text-blue-8', 'blue-8'],
  yellow: ['bg-yellow-1', 'text-yellow-8', 'yellow-8'],
}

const viewMore = () => {
  const selected = showDetails(props.queueData, props.type)
  emit('selected', selected)
}

const callNext = () => {
  Inertia.post('/call_next', { type: props.type, shop: props.shop.id, uuid: props.shop.uuid }, {
    onSuccess: () => emit('called'),
    onError: (error) => console.log(error)
  })
}
</script>
