const getters = {
  windowBreakPoint: state => {
    // This should be same as tailwind. So, it stays in sync with tailwind utility classes
    if (state.windowWidth >= 1200) {
      return 'xl';
    } else if (state.windowWidth >= 992) {
      return 'lg';
    } else if (state.windowWidth >= 768) {
      return 'md';
    } else if (state.windowWidth >= 576) {
      return 'sm';
    } else {
      return 'xs';
    }
  },

  scrollbarTag: state => {
    return state.is_touch_device ? 'div' : 'VuePerfectScrollbar';
  },
  sidebar: state => state.app.sidebar,
  language: state => state.app.language,
  size: state => state.app.size,
  device: state => state.app.device,
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews,
  userId: state => state.user.id,
  token: state => state.user.token,
  avatar: state => state.user.avatar,
  name: state => state.user.name,
  introduction: state => state.user.introduction,
  roles: state => state.user.roles,
  permissions: state => state.user.permissions,
  permissionRoutes: state => state.permission.routes,
  addRoutes: state => state.permission.addRoutes,
//   sidebar: state => state.app.sidebar,
//   language: state => state.app.language,
//   size: state => state.app.size,
//   device: state => state.app.device,
//   visitedViews: state => state.tagsView.visitedViews,
//   cachedViews: state => state.tagsView.cachedViews,
//   userId: state => state.AppActiveUser.id,
//   token: state => state.AppActiveUser.token,
//   avatar: state => state.AppActiveUser.avatar,
//   name: state => state.AppActiveUser.name,
//   introduction: state => state.AppActiveUser.introduction,
//   roles: state => state.AppActiveUser.roles,
//   permissions: state => state.AppActiveUser.permissions,
//   permissionRoutes: state => state.permission.routes,
//   addRoutes: state => state.permission.addRoutes
};
export default getters;
