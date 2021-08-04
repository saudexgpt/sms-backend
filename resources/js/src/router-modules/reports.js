/** When your routing table is too long, you can split it into small modules**/
const reportsRoutes = {
  path: '/reports',
  component: () => import('@/layouts/main/Main.vue'),
  name: 'Reports',
  icon: 'BarChart2Icon',
  i18n: 'Reports',
  meta: {
    roles: ['super', 'admin'],
  },
  children: [
    // //////////////////Settings///////////////////////////

    {
      hidden: false,
      component: () => import('@/views/apps/user/index.vue'),
      path: '/reports/downloadables',
      name: 'Downloadables',
      slug: 'downloadables',
      i18n: 'Downloadables',
      meta: {
        permissions: ['create-users', 'read-users', 'update-users', 'delete-users'],
      },
    },
    {
      hidden: false,
      component: () => import('@/views/pages/ComingSoon.vue'),
      path: '/reports/analysis',
      name: 'Analysis',
      slug: 'analysis',
      i18n: 'Analysis',
      meta: {
        roles: ['admin'],
      },
    },
    {
      hidden: false,
      component: () => import('@/views/pages/ComingSoon.vue'),
      path: '/reports/audit-trail',
      name: 'AuditTrail',
      slug: 'audit-trail',
      i18n: 'Audit Trail',
      meta: {
        roles: ['super'],
      },

    },
  ],

};

export default reportsRoutes;
