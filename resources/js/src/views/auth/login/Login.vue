<template>
  <div class="login-container">
    <div class="login-form">
      <div class="vx-card__title mb-4" align="center">
        <img src="/images/logo/logo.png" alt="logo" width="250" class="mx-auto" >
      </div>
      <vs-input
        v-model="email"
        data-vv-validate-on="blur"
        name="email"
        icon-no-border
        icon="icon icon-user"
        icon-pack="feather"
        label-placeholder="Email"
        class="w-full"
      />

      <vs-input
        v-model="password"
        data-vv-validate-on="blur"
        type="password"
        name="password"
        icon-no-border
        icon="icon icon-lock"
        icon-pack="feather"
        label-placeholder="Password"
        class="w-full mt-6"
      />

      <div class="flex flex-wrap justify-between my-5"/>
      <div class="flex flex-wrap justify-between mb-3">

        <vs-button style="width: 100%" @click="login">Login</vs-button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      password: '',
      checkbox_remember_me: false,
      redirect: undefined,
    };
  },
  watch: {
    $route: {
      handler(route) {
        this.redirect = route.query && route.query.to;
      },
      immediate: true,
    },
  },
  methods: {
    isLoggedIn() {
      // If user is already logged in notify
      if (this.$store.state.user.token) {
        // Close animation if passed as payload
        // this.$vs.loading.close()
        this.$vs.notify({
          title: 'Login Attempt',
          text: 'You are already logged in!',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'warning',
        });
        this.$router.push('/').catch(() => {});
        return true;
      }
      return false;
    },
    login() {
      if (this.isLoggedIn()) {
        return;
      }

      // Loading
      this.$vs.loading();

      const payload = {
        userDetails: {
          email: this.email,
          password: this.password,
          remember_me: this.checkbox_remember_me,
        },
      };
      this.$store
        .dispatch('user/login', payload.userDetails)
        .then(() => {
          this.$vs.notify({
            title: 'Login Success',
            text: 'Welcome',
            color: 'success',
            position: 'top-right',
            icon: 'check_box',
            time: 5000,
          });
          // we load the browser this once
          window.location = '/dashboard'; // this.$router.push({ path: this.redirect || '/' }).catch(() => {})
          this.$vs.loading.close();
        })
        .catch((error) => {
          this.$vs.loading.close();
          this.$vs.notify({
            title: error.response.statusText,
            text: error.response.data.message,
            color: 'danger',
            position: 'top-right',
            icon: 'verified_user',
            time: 5000,
          });
          // console.log(error.response)
        });

      //   this.$store.dispatch('auth/login', payload)
      //     .then(() => { this.$vs.loading.close() })
      //     .catch(error => {
      //       this.$vs.loading.close()
      //       this.$vs.notify({
      //         title: 'Error',
      //         text: error.message,
      //         iconPack: 'feather',
      //         icon: 'icon-alert-circle',
      //         color: 'danger'
      //       })
      //     })
    },
  },
};
</script>

<style rel="stylesheet/scss" lang="scss">
$primary: #1625a5;
$secondary: #666;
$dark_gray: #889aa4;
$light_gray: #eee;
.login-container {
  position: fixed;
  height: 100%;
  width: 100%;
  background-image: url('/images/pages/bg.jpg');
  .login-form {
    position: absolute;
    left: 50;
    right: 0;
    width: 520px;
    max-width: 100%;
    height: 100%;
    padding: 35px 35px 15px 35px;
    background-color: #fff;
    opacity: 0.9;
  }
  .tips {
    font-size: 14px;
    color: #000000;
    margin-bottom: 10px;
    span {
      &:first-of-type {
        margin-right: 16px;
      }
    }
  }
  .svg-container {
    padding: 6px 5px 6px 15px;
    color: $primary;
    vertical-align: middle;
    width: 30px;
    display: inline-block;
  }
  .title {
    font-size: 26px;
    font-weight: 400;
    color: $primary;
    margin: 0px auto 40px auto;
    text-align: center;
    font-weight: bold;
  }
  .show-pwd {
    position: absolute;
    right: 10px;
    top: 7px;
    font-size: 16px;
    color: $primary;
    cursor: pointer;
    user-select: none;
  }
  .set-language {
    color: #fff;
    position: absolute;
    top: 40px;
    right: 35px;
  }
  .md-form label.active {
    font-size: 1.3rem;
  }
  .md-form .prefix {
    top: 0.25rem;
    font-size: 1.5rem;
  }
  .md-form.md-outline .prefix {
    position: absolute;
    top: 0.9rem;
    font-size: 1.9rem;
    -webkit-transition: color 0.2s;
    transition: color 0.2s;
  }
  .md-form.md-outline .form-control {
    padding: 1rem;
  }
}
</style>
