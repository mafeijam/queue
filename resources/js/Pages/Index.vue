<template lang="pug">
q-layout(view="hHh lpr fFf")
  q-page-container.bg-grey-1
    q-page.flex.items-center(style="max-width: 800px; margin: auto")
      q-card.shadow-1.full-width(:flat="$q.platform.is.mobile")
        q-card-section.q-pa-lg
          q-form(@submit.prevent="createShop")
            .q-gutter-lg
              .flex.justify-center
                img(src="https://q.jamwong.me/logo.svg" alt="logo" style="width: 100px;")
              .text-h4.text-teal 電子排隊系統

              .bg-light-blue-1.q-pa-lg.text-center
                .text-h6.text-light-blue-6.q-mb-md 今時今日食飯等位仲邊洗用紙同筆架？！ 仲要D 客企係門口等？！
                .q-gutter-xs.text-grey-8
                  .text-body2 商戶只需要有一部平板電腦，客戶只需要有一部智能手機
                  .text-body2 無需安裝任何APP，無需註冊任何帳戶，只需要一個瀏覽器
                  .text-body2 實時更新，隨時隨地都可以睇到最新等位情況！

              .text-h6.text-grey-7 建立排隊服務
              q-input(
                label="店舖名稱"
                v-model="form.name"
                filled
                :error="!!errorMsg.name"
                :error-message="errorMsg.name"
                @update:model-value="errorMsg.name = null"
                hide-bottom-space
              )
                template(#prepend)
                  q-icon(name="store")
              q-input(
                label="電郵"
                v-model="form.email"
                filled
                :error="!!errorMsg.email"
                :error-message="errorMsg.email"
                @update:model-value="errorMsg.email = null"
                hide-bottom-space
                hint="用於接收後台網址及密碼"
              )
                template(#prepend)
                  q-icon(name="email")
              .text-right
                q-btn(label="建立" color="primary" size="lg" icon="check" type="submit" :loading="loading")

        .text-caption.flex.items-center.justify-center.text-grey-6.q-pa-md Made with <img src="https://q.jamwong.me/heart.png" style="width: 20px;" class="q-mx-xs" /> by Jam Wong
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
import { useQuasar } from 'quasar'

const loading = ref(false)

const form = reactive({
  name: null,
  email: null
})

const errorMsg = ref({
  name: null,
  email: null
})

const message = computed(() => usePage().props.value.message)

const $q = useQuasar()

watch(message, message => {
  if (message) {
    $q.dialog({
      title: '系統信息',
      message,
      class: 'text-grey-8',
      transitionShow: 'jump-down'
    }).onDismiss(() => usePage().props.value.message = null)
  }
}, { immediate: true })

const createShop = () => Inertia.post('/create_shop', form, {
  onStart: () => loading.value = true,
  onFinish: () => loading.value = false,
  onError: error => errorMsg.value = error
})
</script>
