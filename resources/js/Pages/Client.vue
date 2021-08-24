<template lang="pug">
q-layout(view="hHh lpr fFf")
  q-page-container.bg-grey-1
    q-page.window-height(style="max-width: 800px; margin: auto")
      q-card.shadow-1.window-height(flat)
        q-card-section.q-pa-md
          .q-gutter-md
            .text-h4.text-teal 電子排隊系統
            .flex
              .text-h5.text-grey-7.q-mr-lg 店舖名稱
              .text-h5.text-indigo-7 {{ shop.name }}

        q-card-section.q-pa-md(v-if="ticket.exists")
          .text-h6.q-mb-md 已經獲取票號
          q-btn(label="查看票號" @click="ticketOpen(ticket.link)" color="purple" size="lg")

        q-card-section.q-pa-md(v-else)
          .row.q-col-gutter-md.text-subtitle1
            .col-12
              .text-h5.text-pink 請選擇人數並取票
            .col-12
              q-btn.full-width(
                label="A 1 ~ 2人"
                color="red-1"
                text-color="red-8"
                size="1.8rem"
                @click="getTicket('A')"
              )
            .col-12
              q-btn.full-width(
                label="B 3 ~ 4人"
                color="green-1"
                text-color="green-8"
                size="1.8rem"
                @click="getTicket('B')"
              )
            .col-12
              q-btn.full-width(
                label="C 5 ~ 6人"
                color="blue-1"
                text-color="blue-8"
                size="1.8rem"
                @click="getTicket('C')"
              )
            .col-12
              q-btn.full-width(
                label="D 7人或以上"
                color="yellow-1"
                text-color="yellow-8"
                size="1.8rem"
                @click="getTicket('D')"
              )
</template>

<script setup>
import { computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
  shop: Object,
  ticket: Object
})

const getTicket = type => Inertia.post('/get_ticket', {
  type,
  shop: props.shop.id,
  uuid: props.shop.uuid
}, {
  onError: (error) => console.log(error)
})

const ticketOpen = route => window.location = route
</script>
