/** When your routing table is too long, you can split it into small modules**/
const adminRoutes = {
  path: '/sales',
  component: () => import('@/layouts/main/Main.vue'),
  name: 'Sales',
  icon: 'DollarSignIcon',
  i18n: 'Sales',
  slug: 'sales',
  redirect: '/sales/view',
  permissions: [],
  children: [
    {
      hidden: true,
      path: '/sales/view',
      component: () => import('@/views/apps/sales/index'),
    },
  ],
};

export default adminRoutes;
