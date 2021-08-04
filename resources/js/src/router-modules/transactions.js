/** When your routing table is too long, you can split it into small modules**/
const transactionsRoutes = {
  path: '/transactions',
  component: () => import('@/layouts/main/Main.vue'),
  name: 'Transactions',
  icon: 'LinkIcon',
  i18n: 'Transactions',
  redirect: '/transactions/sales',
  meta: {
    roles: ['super', 'admin'],
  },
  children: [
    // //////////////////transactions///////////////////////////
    {
      hidden: false,
      component: () => import('@/views/pages/ComingSoon.vue'),
      path: '/transactions/orders',
      name: 'Orders',
      slug: 'orders',
      i18n: 'Orders',
      meta: {
        // permissions: [],
      },
    },
    {
      hidden: false,
      component: () => import('@/views/apps/user/index.vue'),
      path: '/transactions/sales',
      name: 'Sales',
      slug: 'sales',
      i18n: 'Sales',
      meta: {
        // permissions: [],
      },
    },
    {
      hidden: false,
      component: () => import('@/views/pages/ComingSoon.vue'),
      path: '/transactions/payments',
      name: 'Payments',
      slug: 'payments',
      i18n: 'Payments',
      meta: {
        // permissions: [],
      },
    },
  ],

};

export default transactionsRoutes;
