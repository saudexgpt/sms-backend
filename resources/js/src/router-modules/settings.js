/** When your routing table is too long, you can split it into small modules**/
const settingsRoutes = {
  path: '/settings',
  component: () => import('@/layouts/main/Main.vue'),
  name: 'Settings',
  icon: 'SettingsIcon',
  i18n: 'Admin Settings',
  meta: {
    roles: ['super', 'admin'],
  },
  children: [
    // //////////////////Settings///////////////////////////

    {
      hidden: false,
      component: () => import('@/views/apps/user/index.vue'),
      path: '/settings/manage-users',
      name: 'ManageUsers',
      slug: 'manage-users',
      i18n: 'ManageUsers',
      meta: {
        permissions: ['create-users', 'read-users', 'update-users', 'delete-users'],
      },
    },
    {
      hidden: false,
      component: () => import('@/views/pages/ComingSoon.vue'),
      path: '/settings/general-settings',
      name: 'GeneralSettings',
      slug: 'general-settings',
      i18n: 'GeneralSettings',
      meta: {
        roles: ['admin'],
      },
    },
    {
      hidden: false,
      component: () => import('@/views/pages/ComingSoon.vue'),
      path: '/access-control',
      name: 'Permissions',
      slug: 'external',
      i18n: 'User Permissions',
      target: '_blank',
      meta: {
        roles: ['super'],
      },

    },
  ],

};

export default settingsRoutes;
