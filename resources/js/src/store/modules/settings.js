import defaultSettings from '@/settings'
const { showSettings, tagsView, fixedHeader, sidebarLogo, theme } = defaultSettings

const state = {
  theme,
  showSettings,
  tagsView,
  fixedHeader,
  sidebarLogo
}

const mutations = {
  CHANGE_SETTING: (state, { key, value }) => {
    if (state.hasOwnProperty(key)) {
      state[key] = value
    }
  }
}

const actions = {
  changeSetting ({ commit }, data) {
    commit('CHANGE_SETTING', data)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}

