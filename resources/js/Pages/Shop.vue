<template lang="pug">
q-dialog(v-model="show" maximized transition-show="jump-down" transition-hide="jump-up")
  q-card
    q-bar.bg-blue-8.text-white.fixed-top.z-top
      q-space
      q-btn(dense flat icon="close" v-close-popup color="white")
    q-card-section(style="max-width: 600px; margin: auto")
      .shadow-1
        q-markup-table.q-mt-xl.bg-grey-1(flat)
          thead
            tr.text-grey-8
              th.text-left(width="20%") 票號
              th.text-right(width="40%") 取票時間
              th.text-right(width="40%") 入坐時間
          tbody
            tr(v-for="s in selected")
              td.text-left {{ s.ticket_type }}{{ s.ticket_number }}
              td.text-right {{ s.created_at }}
              td.text-right {{ s.available ? s.updated_at :　'-' }}


q-layout(view="hHh lpr fFf")
  q-page-container.bg-grey-1
    q-page.flex.items-center(:padding="$q.platform.is.desktop" style="max-width: 1200px; margin: auto")
      q-card.shadow-1.full-width(:flat="$q.platform.is.mobile")
        q-card-section.q-pa-lg
          .q-gutter-lg
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
            .col-3.text-center
              .q-pa-sm.bg-red-1
                .text-right
                  q-btn(
                    label="查閱明細" flat icon="list" dense color="red-8"
                    @click="viewMore('A')" :disable="noDetails(queueData, 'A')"
                  )
              .q-pa-lg.bg-red-1
                .text-h5.text-weight-bold.text-red-8.q-mb-lg A 1 ~ 2人
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新入座票號
                .text-h3.text-grey-9 {{ showTicketAvailable(queueData, 'A') }}
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新獲取票號
                .text-h3.text-grey-9 {{ showTicketWaiting(queueData, 'A') }}
                q-btn.full-width.q-mt-xl(
                  label="下一個" :color="noNext(queueData, 'A') ? 'grey-5' : 'red'"
                  size="lg"
                  @click="callNext('A')"
                  :disable="noNext(queueData, 'A')"
                )

            .col-3.text-center
              .q-pa-sm.bg-green-1
                  .text-right
                    q-btn(
                      label="查閱明細" flat icon="list" dense color="green-8"
                      @click="viewMore('B')" :disable="noDetails(queueData, 'B')"
                    )
              .q-pa-lg.bg-green-1
                .text-h5.text-weight-bold.text-green-8.q-mb-lg B 3 ~ 4人
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新入座票號
                .text-h3.text-grey-9 {{ showTicketAvailable(queueData, 'B') }}
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新獲取票號
                .text-h3.text-grey-9 {{ showTicketWaiting(queueData, 'B') }}
                q-btn.full-width.q-mt-xl(
                  label="下一個" :color="noNext(queueData, 'B') ? 'grey-5' : 'green'"
                  size="lg"
                  @click="callNext('B')"
                  :disable="noNext(queueData, 'B')"
                )

            .col-3.text-center
              .q-pa-sm.bg-blue-1
                  .text-right
                    q-btn(
                      label="查閱明細" flat icon="list" dense color="blue-8"
                      @click="viewMore('C')" :disable="noDetails(queueData, 'C')"
                    )
              .q-pa-lg.bg-blue-1
                .text-h5.text-weight-bold.text-blue-8.q-mb-lg C 5 ~ 6人
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新入座票號
                .text-h3.text-grey-9 {{ showTicketAvailable(queueData, 'C') }}
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新獲取票號
                .text-h3.text-grey-9 {{ showTicketWaiting(queueData, 'C') }}
                q-btn.full-width.q-mt-xl(
                  label="下一個" :color="noNext(queueData, 'C') ? 'grey-5' : 'blue'"
                  size="lg"
                  @click="callNext('C')"
                  :disable="noNext(queueData, 'C')"
                )

            .col-3.text-center
              .q-pa-sm.bg-yellow-1
                .text-right
                  q-btn(
                    label="查閱明細" flat icon="list" dense color="yellow-8"
                    @click="viewMore('D')" :disable="noDetails(queueData, 'D')"
                  )
              .q-pa-lg.bg-yellow-1
                .text-h5.text-weight-bold.text-yellow-8.q-mb-lg D 7人或以上
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新入座票號
                .text-h3.text-grey-9 {{ showTicketAvailable(queueData, 'D') }}
                q-separator(spaced="lg")
                .text-h6.text-grey-7 最新獲取票號
                .text-h3.text-grey-9 {{ showTicketWaiting(queueData, 'D') }}
                q-btn.full-width.q-mt-xl(
                  label="下一個" :color="noNext(queueData, 'D') ? 'grey-5' : 'yellow'"
                  size="lg"
                  @click="callNext('D')"
                  :disable="noNext(queueData, 'D')"
                )
</template>

<script setup>
import { computed, ref } from 'vue'
import { showTicketAvailable, showTicketWaiting, showDetails, noDetails, noNext } from '@/useTicket'
import { Inertia } from '@inertiajs/inertia'
import { useQuasar } from 'quasar'

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
  audio.play()
  queueEvent.value = evt.queues
}

const callNext = type => {
  Inertia.post('/call_next', { type, shop: props.shop.id, uuid: props.shop.uuid }, {
    onSuccess: () => queueEvent.value = null,
    onError: (error) => console.log(error)
  })
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

const viewMore = type => {
  selected.value = showDetails(queueData.value, type)
  show.value = true
}

window.Echo.channel(`queue.${props.shop.uuid}`)
  .listen('QueueCreated', onEvent)

</script>
