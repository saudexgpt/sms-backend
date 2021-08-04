<!-- =========================================================================================
    File Name: TheFooter.vue
    Description: Footer component
    Component Name: TheFooter
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template functional>
  <footer :class="classes" class="the-footer flex-wrap justify-between">
    <p>
      <span>COPYRIGHT &copy;</span>
      <span>{{ new Date().getFullYear() }} </span>
      <a href="#" rel="nofollow"><span>GPL-SALES</span></a>
    </p>
    <span class="md:flex hidden items-center">
      <span>All rights Reserved</span>
    </span>

    <!-- buyNow component -->
    <!-- <component :is="injections.components.BuyNow"></component> -->
  </footer>
</template>

<script>
export default {
  name: 'TheFooter',
  props: {
    classes: {
      type: String,
      default: () => (''),
    },
    title: {
      type: String,
      default: () => ('AKIRS'),
    },
  },
};
</script>
