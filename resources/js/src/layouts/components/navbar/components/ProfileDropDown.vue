<template>
  <div v-if="activeUserInfo.name" class="the-navbar__user-meta flex items-center">

    <div class="text-right leading-tight hidden sm:block">
      <small>Welcome</small>
      <p class="font-semibold">{{ activeUserInfo.name }}</p>

    </div>

    <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer">

      <div class="con-img ml-3">
        <img v-if="activeUserInfo.avatar" key="onlineImg" :src="activeUserInfo.avatar" alt="user-img" width="40" height="40" class="rounded-full shadow-md cursor-pointer block" >
      </div>

      <vs-dropdown-menu class="notification-dropdown dropdown-custom vx-navbar-dropdown">
        <div class="notification-top text-center p-5 bg-primary text-white" align="center">
          <img v-if="activeUserInfo.avatar" key="onlineImg" :src="activeUserInfo.avatar" alt="user-img" width="100" class="rounded-full shadow-md" >
          <p class="font-semibold">{{ activeUserInfo.name }}</p>
        </div>
        <br>
        <vs-row>
          <vs-col vs-type="flex" vs-justify="center" vs-align="center" vs-w="6">
            <vs-button color="primary" type="border" @click="$router.push('/pages/profile').catch(() => {})">
              <feather-icon icon="UserIcon" svg-classes="w-4 h-4" />
              <span class="ml-2">Profile</span>
            </vs-button>
          </vs-col>
          <vs-col vs-type="flex" vs-justify="center" vs-align="center" vs-w="6">
            <vs-button color="primary" type="border" @click="logout">
              <feather-icon icon="LogOutIcon" svg-classes="w-4 h-4" />
              <span class="ml-2">Logout</span>
            </vs-button>
          </vs-col>
        </vs-row>
        <br>
      </vs-dropdown-menu>
    </vs-dropdown>
  </div>
</template>

<script>
import firebase from 'firebase/app';
import 'firebase/auth';

export default {
  data() {
    return {

    };
  },
  computed: {
    activeUserInfo() {
      return this.$store.state.user;
    },
  },
  methods: {
    async logout() {
      await this.$store.dispatch('user/logout');
      this.$router.push('/login').catch(() => {});
    },
    // logout () {

    //   // If JWT login
    //   if (localStorage.getItem('accessToken')) {
    //     localStorage.removeItem('accessToken')
    //     localStorage.setItem('loggedIn', 'false')
    //     this.$router.push('/pages/login').catch(() => {})
    //   }

    //   // Change role on logout. Same value as initialRole of acj.js
    //   this.$acl.change('admin')
    //   localStorage.removeItem('userInfo')

    //   // This is just for demo Purpose. If user clicks on logout -> redirect
    //   this.$router.push('/pages/login').catch(() => {})
    // }
  },
};
</script>
