import Vue from 'vue'
import Vuex from 'vuex'
import getters from './getters'
import camelCase from 'camelcase'
import mutations from './mutations'
import state from './state'
import actions from './actions'
Vue.use(Vuex)

// https://webpack.js.org/guides/dependency-management/#requirecontext
const modulesFiles = require.context('./modules', false, /\.js$/)

// you do not need `import app from './modules/app'`
// it will auto require all vuex module from modules file
const modules = modulesFiles.keys().reduce((modules, modulePath) => {
  // set './app.js' => 'app'
  const moduleName = camelCase(modulePath.replace(/^\.\/(.*)\.\w+$/, '$1'))
  const value = modulesFiles(modulePath)
  modules[moduleName] = value.default
  return modules
}, {})

const store = new Vuex.Store({
  modules,
  actions,
  state,
  mutations,
  getters,
  strict: process.env.NODE_ENV !== 'production'
})

export default store
