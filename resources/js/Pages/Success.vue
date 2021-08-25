<template lang="pug">
q-layout(view="hHh lpr fFf")
  q-page-container.bg-grey-1
    q-page.flex.items-center(style="max-width: 800px; margin: auto")
      q-card.shadow-1.full-width(:flat="$q.platform.is.mobile")
        q-card-section.q-pa-lg
          .q-gutter-lg
            .flex.justify-center
              a(href="https://q.jamwong.me")
                img(src="https://q.jamwong.me/logo.svg" alt="logo" style="width: 100px;")
            .text-h4.text-teal 電子排隊系統
            .bg-teal-1.q-pa-md.text-subtitle1.text-teal-8
              q-icon.q-mr-md(name="announcement" size="2rem")
              span 付款成功
            .text-h2.text-grey-8.text-weight-medium 多謝你杯咖啡！
</template>
