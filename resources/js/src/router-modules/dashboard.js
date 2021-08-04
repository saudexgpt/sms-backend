/** When your routing table is too long, you can split it into small modules**/
const adminRoutes = {
  path: '/',
  component: () => import('@/layouts/main/Main.vue'),
  name: 'Dashboard',
  icon: 'HomeIcon',
  i18n: 'Dashboard',
  slug: 'dashboard',
  redirect: '/dashboard',
  roles: ['admin', 'super'],
  children: [
    {
      hidden: true,
      path: '/dashboard',
      component: () => import('@/views/dashboard'),
    },
  ],
};

export default adminRoutes;
