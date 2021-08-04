/* =========================================================================================
  File Name: main.js
  Description: main vue(js) file
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
import './styles/element-variables.scss';
import '@/styles/index.scss'; // global css
import Vue from 'vue';
import App from './App.vue';
import ElementUI from 'element-ui';

import locale from 'element-ui/lib/locale/lang/en';
import * as VueGoogleMaps from 'vue2-google-maps';
Vue.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyBT3RaSQYPAKUtw_gr4w6pInQFU05aPRJk',
    libraries: 'places', // This is required if you use the Autocomplete plugin
    // OR: libraries: 'places,drawing'
    // OR: libraries: 'places,drawing,visualization'
    // (as you require)

    // // If you want to set the version, you can do so:
    // v: '3.26',
  },

  // // If you intend to programmatically custom event listener code
  // // (e.g. `this.$refs.gmap.$on('zoom_changed', someFunc)`)
  // // instead of going through Vue templates (e.g. `<GmapMap @zoom_changed="someFunc">`)
  // // you might need to turn this on.
  // autobindAllEvents: false,

  // // If you want to manually install components, e.g.
  // // import {GmapMarker} from 'vue2-google-maps/src/components/marker'
  // // Vue.component('GmapMarker', GmapMarker)
  // // then disable the following:
  // installComponents: true,
});
Vue.use(ElementUI, { locale });
// Vuesax Component Framework
import Vuesax from 'vuesax';

Vue.use(Vuesax);
// axios
// import axios from './axios.js'
// Vue.prototype.$http = axios

// API Calls
// import './http/requests'

// Theme Configurations
import '../themeConfig.js';
import '@/permission';

// // Firebase
// import '@/firebase/firebaseConfig'

// // Auth0 Plugin
// import AuthPlugin from './plugins/auth'
// Vue.use(AuthPlugin)

// ACL
// import acl from './acl/acl'

// Globally Registered Components
import './globalComponents.js';

// Vue Router
import router from './router.js';

// Vuex Store
import store from './store';
// import store from './store'

// i18n
import i18n from './i18n/i18n';

// Vuexy Admin Filters
import './filters/filters';

// Clipboard
import VueClipboard from 'vue-clipboard2';
Vue.use(VueClipboard);

// // Tour
// import VueTour from 'vue-tour';
// Vue.use(VueTour);
// require('vue-tour/dist/vue-tour.css');

// // VeeValidate
import VeeValidate from 'vee-validate';
Vue.use(VeeValidate);

import { ServerTable, ClientTable, Event } from 'vue-tables-2';
Vue.use(ClientTable);
Vue.use(ServerTable);
Vue.use(Event);
// Google Maps
// import * as VueGoogleMaps from 'vue2-google-maps'
// Vue.use(VueGoogleMaps, {
//   load: {
//     // Add your API key here
//     key: 'AIzaSyB4DDathvvwuwlwnUu7F4Sow3oU22y5T1Y',
//     libraries: 'places' // This is required if you use the Auto complete plug-in
//   }
// })

// Vuejs - Vue wrapper for hammerjs
import { VueHammer } from 'vue2-hammer';
Vue.use(VueHammer);

// PrismJS
import 'prismjs';
import 'prismjs/themes/prism-tomorrow.css';

// Feather font icon
require('@assets/css/iconfont.css');

// Vue select css
// Note: In latest version you have to add it separately
// import 'vue-select/dist/vue-select.css';

Vue.config.productionTip = false;

new Vue({
  router,
  store,
  i18n,
  // acl,
  render: h => h(App),
}).$mount('#app');
