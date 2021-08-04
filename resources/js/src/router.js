import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

/* Layout */
// import Layout from './layouts/main/Main.vue'

/**
 * Note: sub-menu only appear when route children.length >= 1
 * Detail see: https://panjiachen.github.io/vue-element-admin-site/guide/essentials/router-and-nav.html
 *
 * hidden: true                   if set true, item will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu
 *                                if not set alwaysShow, when item has more than one children route,
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noRedirect           if set noRedirect will no redirect in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    roles: ['super','editor']    control the page roles (you can set multiple roles)
    title: 'title'               the name show in sidebar and breadcrumb (recommend set)
    icon: 'svg-name'/'el-icon-x' the icon show in the sidebar
    noCache: true                if set true, the page will no be cached(default is false)
    affix: true                  if set true, the tag will affix in the tags-view
    breadcrumb: false            if set false, the item will hidden in breadcrumb(default is true)
    activeMenu: '/example/list'  if set path, the sidebar will highlight the path you set
  }
 */

/**
 * constantRoutes
 * a base page that does not have permission requirements
 * all roles can be accessed
 */
import dashboardRoutes from '@/router-modules/dashboard';
import settingsRoutes from '@/router-modules/settings';
import reportsRoutes from '@/router-modules/reports';
import schedulesRoutes from '@/router-modules/schedules';
import transactionsRoutes from '@/router-modules/transactions';
import customersRoutes from '@/router-modules/customers';
import inventoryRoutes from '@/router-modules/inventories';
export const constantRoutes = [

  // =============================================================================
  // PAGES
  // =============================================================================
  {

    hidden: true,
    path: '/callback',
    name: 'auth-callback',
    component: () => import('@/views/Callback.vue'),
  },
  {
    hidden: true,
    path: '/login',
    name: 'page-login',
    component: () => import('@/views/auth/login/Login.vue'),
  },
  {
    hidden: true,
    path: '/lock-screen',
    name: 'page-lock-screen',
    component: () => import('@/views/pages/LockScreen.vue'),
  },
  {
    hidden: true,
    path: '/comingsoon',
    name: 'page-coming-soon',
    component: () => import('@/views/pages/ComingSoon.vue'),
  },
  {
    hidden: true,
    path: '/error-404',
    name: 'page-error-404',
    component: () => import('@/views/pages/Error404.vue'),
  },
  {
    hidden: true,
    path: '/error-500',
    name: 'page-error-500',
    component: () => import('@/views/pages/Error500.vue'),
  },
  {
    hidden: true,
    path: '/not-authorized',
    name: 'page-not-authorized',
    component: () => import('@/views/pages/NotAuthorized.vue'),
  },
  {
    hidden: true,
    path: '/maintenance',
    name: 'page-maintenance',
    component: () => import('@/views/pages/Maintenance.vue'),
  },

];

/**
 * asyncRoutes
 * the routes that need to be dynamically loaded based on user roles
 */
export const asyncRoutes = [

  // =============================================================================
  // MAIN LAYOUT ROUTES
  // =============================================================================
  dashboardRoutes,
  customersRoutes,
  schedulesRoutes,
  transactionsRoutes,
  inventoryRoutes,
  reportsRoutes,
  settingsRoutes,
  {
    path: '*',
    redirect: '/error-404',
    hidden: true,
  },
];

const createRouter = () => new Router({
  // mode: 'history', // require service support
  mode: 'history',
  base: '/',
  scrollBehavior() {
    return { x: 0, y: 0 };
  },
  routes: constantRoutes,
});

const router = createRouter();

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter();
  router.matcher = newRouter.matcher; // reset router
}

export default router;
