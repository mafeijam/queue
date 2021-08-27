<template lang="pug">
q-dialog(v-model="show" maximized transition-show="jump-down" transition-hide="jump-up")
  q-card
    q-bar.bg-blue-8.text-white.fixed-top.z-top
      q-space
      q-btn(dense flat icon="close" v-close-popup color="white")
    q-card-section(style="max-width: 700px; margin: auto")
      .shadow-1
        q-markup-table.q-mt-xl(flat)
          thead
            tr.text-grey-8
              th.text-left(width="10%") 票號
              th.text-right(width="30%") 取票時間
              th.text-right(width="30%") 入坐時間
              th.text-right(width="30%") 取消時間
          tbody
            tr(v-for="s in selected" :class="s.cancelled_at ? 'bg-purple-1' : ''")
              td.text-left {{ s.ticket_type }}{{ s.ticket_number }}
              td.text-right {{ s.created_at }}
              td.text-right {{ s.available ? s.updated_at :　'-' }}
              td.text-right {{ s.cancelled_at ?? '-' }}

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
              .text-h6.text-indigo-7 {{ shop.name }}
            .text-right
              q-btn(label="重設" icon="restart_alt" @click="reset" color="pink")

        q-card-section.q-pt-none.q-pb-lg.q-px-lg
          .row.q-col-gutter-lg.text-subtitle1
            TicketNextCard(
              color="red" title="A 1 ~ 2人" type="A" :queueData="queueData"
              :shop="shop" @selected="viewMore" @called="queueEvent = null"
            )
            TicketNextCard(
              color="green" title="B 3 ~ 4人" type="B" :queueData="queueData"
              :shop="shop" @selected="viewMore" @called="queueEvent = null"
            )

            TicketNextCard(
              color="blue" title="C 5 ~ 6人" type="C" :queueData="queueData"
              :shop="shop" @selected="viewMore" @called="queueEvent = null"
            )

            TicketNextCard(
              color="yellow" title="D 7人或以上" type="D" :queueData="queueData"
              :shop="shop" @selected="viewMore" @called="queueEvent = null"
            )
</template>

<script setup>
import { computed, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useQuasar } from 'quasar'
import TicketNextCard from '@/Components/TicketNextCard.vue'

const props = defineProps({
  shop: Object,
  queues: Object,
})

const show = ref(false)
const selected = ref(null)

const queueEvent = ref(null)
const queueData = computed(() => queueEvent.value === null ? props.queues : queueEvent.value)

const audio = new Audio('https://q.jamwong.me/mixkit-happy-bells-notification-937.wav')

const onEvent = evt => {
  queueEvent.value = evt.queues
  audio.play()
}

const $q = useQuasar()

const reset = () => {
  $q.dialog({
      title: '系統信息',
      message: '將會刪除所有票號，請確認是否繼續？',
      class: 'text-pink-6',
      transitionShow: 'jump-down',
      cancel: true,
      ok: {
        color: 'text-pink-6',
        label: '確認',
        flat: true
      },
      cancel: {
        color: 'grey',
        label: '取消',
        flat: true
      }
  }).onOk(() => {
    Inertia.delete(`/reset/${props.shop.uuid}`, {
      onSuccess: () => queueEvent.value = null,
      onError: (error) => console.log(error)
    })
  })
}

const viewMore = evt => {
  selected.value = evt
  show.value = true
}

window.Echo.channel(`queue.${props.shop.uuid}`)
  .listen('QueueCreated', onEvent)
  .listen('QueueCancelled', evt => queueEvent.value = evt.queues)

</script>
