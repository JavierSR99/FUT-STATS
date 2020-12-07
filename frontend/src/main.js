import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

// importamos vuetify para realizar el diseño de la web
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
Vue.use(Vuetify)

// importamos vuelidate para validar datos
import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)

// importamos axios para la comunicación cliente-servidor
import axios from 'axios'
import VueAxios from 'vue-axios'
import vuetify from './plugins/vuetify';
Vue.use(VueAxios, axios)
axios.default.baseURL = 'http://localhost:/FUT STATS/backend/api.php';


// importamos fontawesome para iconos
import { library } from '@fortawesome/fontawesome-svg-core'
import { faFutbol, faThumbsUp, faThumbsDown, faAngleDoubleLeft, faBars, faArrowLeft, faArrowRight } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faFutbol, faThumbsUp, faThumbsDown, faAngleDoubleLeft, faBars, faArrowLeft, faArrowRight)

Vue.component('font-awesome-icon', FontAwesomeIcon)


Vue.config.productionTip = false



new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
