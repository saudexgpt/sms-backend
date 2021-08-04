<!-- =========================================================================================
    File Name: Alert.vue
    Description: Alert
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
  <div id="alert-demo">
    <alert-default />
    <alert-title />
    <alert-color />
    <alert-closable />
    <alert-icon />
    <alert-example />
  </div>
</template>

<script>
import AlertDefault from "./AlertDefault.vue";
import AlertTitle from "./AlertTitle.vue";
import AlertColor from "./AlertColor.vue";
import AlertClosable from "./AlertClosable.vue";
import AlertIcon from "./AlertIcon.vue";
import AlertExample from "./AlertExample.vue";

export default {
  components: {
    AlertDefault,
    AlertTitle,
    AlertColor,
    AlertClosable,
    AlertIcon,
    AlertExample,
  },
};
</script>
