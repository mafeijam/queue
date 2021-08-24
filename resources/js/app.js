import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { Quasar, AppFullscreen, Dialog } from 'quasar'
import 'quasar/dist/quasar.prod.css'

import { useEcho } from './useEcho'

const el = document.getElementById('app')
const page = JSON.parse(el.dataset.page)

window.Echo = useEcho(page.props.pusher.key)

createInertiaApp({
  resolve: name => import(`./Pages/${name}`),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(Quasar, {
        plugins: {
          AppFullscreen, Dialog
        }
      })
      .mount(el)
  },
})
