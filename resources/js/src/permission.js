import router from '@/router';
// import store from './store'
import store from '@/store';
import { getToken } from '@/utils/auth'; // get token from cookie
import getPageTitle from '@/utils/get-page-title';

const whiteList = ['/login', '/forgot-password', '/register', '/reset-password', '/maintenance']; // no redirect whitelist

router.beforeEach(async(to, from, next) => {
  // set page title
  document.title = getPageTitle(to.meta.title);

  // determine whether the user has logged in
  const hasToken = getToken();

  if (hasToken) {
    if (to.path === '/login') {
      // if is logged in, redirect to the home page
      next({ path: '/' });
    } else {
      // determine whether the user has obtained his permission roles through getInfo
      const hasRoles = store.getters.roles && store.getters.roles.length > 0;

      if (hasRoles) {
        next();
      } else {
        try {
          // get user info
          // note: roles must be a object array! such as: ['admin'] or ,['manager','editor']
          const { roles, permissions } = await store.dispatch('user/getInfo');
          // generate accessible routes map based on roles
          // const accessRoutes = await store.dispatch('permission/generateRoutes', roles, permissions);
          store.dispatch('permission/generateRoutes', { roles, permissions }).then(response => {
            // dynamically add accessible routes
            router.addRoutes(response);
            // hack method to ensure that addRoutes is complete
            // set the replace: true, so the navigation will not leave a history record
            next({ ...to, replace: true });
          });
        } catch (error) {
          // remove token and go to login page to re-login
          await store.dispatch('user/resetToken');
          // router.push({ path: '/login', query: { to: to.path } }).catch(() => { })
          next(`/login?redirect=${to.path}`);
        }
      }
    }
  } else {
    /* has no token*/
    // console.log(whiteList.indexOf(to.matched[0] ? to.matched[0].path : ''))
    // eslint-disable-next-line no-lonely-if
    if (whiteList.includes(to.path)) {
      // in the free login whitelist, go directly
      next();
    } else {
      // other pages that do not have permission to access are redirected to the login page.
      // router.push({ path: '/login', query: { to: to.path } }).catch(() => { })
      next('/login');
    }
    // router.push({ path: '/login', query: { to: to.path } })
    // next(`/login?redirect=${to.path}`)
  }
});
router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg');
  if (appLoading) {
    appLoading.style.display = 'none';
  }
});
