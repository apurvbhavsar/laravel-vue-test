import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import App from '~/components/App'

import '~/plugins'
import '~/components'

import Gate from './policy/Gate';
Vue.prototype.$gate = new Gate(window.user);

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
    store,
    router,
    ...App
})
