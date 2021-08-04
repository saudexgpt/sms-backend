// import auth from '@/auth/authService'
const state = {
  isUserLoggedIn: () => {
    const localStorageKey = 'loggedIn'

    const tokenExpiryKey = 'tokenExpiry'
    let isAuthenticated = false

    // get firebase current user
    // const firebaseCurrentUser = firebase.auth().currentUser

    if (new Date(Date.now()) < new Date(localStorage.getItem(tokenExpiryKey)) &&
            localStorage.getItem(localStorageKey) === 'true') {
      isAuthenticated = true
    } else { isAuthenticated = false }

    return localStorage.getItem('userInfo') && isAuthenticated
  },

  user: {
    id: null,
    // token: localStorage.getItem('accessToken'),
    name: '',
    avatar: '',
    introduction: '',
    roles: [],
    permissions: []
  }

}

export default {
  namespaced: true,
  state
}

