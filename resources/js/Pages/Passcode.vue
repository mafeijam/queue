<template lang="pug">
q-layout(view="hHh lpr fFf")
  q-page-container.bg-grey-1
    q-page.flex.items-center(padding style="max-width: 600px; margin: auto")
      q-card.shadow-1.full-width(:flat="$q.platform.is.mobile")
        q-card-section.q-pa-lg
          q-form(@submit.prevent="login")
            .q-gutter-lg
              .text-h4.text-teal 電子排隊系統

              .flex
                .text-h6.text-grey-7.q-mr-lg 店舖名稱
                .text-h6.text-indigo-7 {{ shop.name }}

              q-input(
                label="密碼"
                v-model="passcode"
                filled
                :error="!!errorMsg.passcode"
                :error-message="errorMsg.passcode"
                @update:model-value="errorMsg.passcode = null"
                hide-bottom-space
                type="password"
                autofocus
              )
                template(#prepend)
                  q-icon(name="lock")

              .text-right
                q-btn(label="登入" color="primary" size="lg" icon="check" type="submit")
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'

const props = defineProps({
  shop: Object
})

const passcode = ref(null)
const errorMsg = ref({
  passcode: null
})

const msg = computed(() => usePage().props.value.message)

watch(msg, msg => errorMsg.value.passcode = msg, { immediate: true })

const login = () => Inertia.post('/passcode', { passcode: passcode.value, shop: props.shop.id }, {
  onError: error => errorMsg.value = error
})
</script>
