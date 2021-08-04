<template>
  <div v-if="currentRole !== ''" class="dashboard-container">
    <component :is="currentRole" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import superAdminDashboard from './super';
import adminDashboard from './admin';
import editorDashboard from './editor';

export default {
  name: 'Dashboard',
  components: { superAdminDashboard, adminDashboard, editorDashboard },
  data() {
    return {
      currentRole: '',
    };
  },
  computed: {
    ...mapGetters([
      'roles',
    ]),
  },
  created() {
    if (this.roles.includes('super')) {
      this.currentRole = 'superAdminDashboard';
    } else if (this.roles.includes('admin')) {
      this.currentRole = 'adminDashboard';
    } else {
      this.currentRole = 'editorDashboard';
    }
  },
};
</script>
