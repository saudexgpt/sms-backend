import Vue from 'vue';
import { AclInstaller, AclCreate, AclRule } from 'vue-acl';
import router from '@/router';

Vue.use(AclInstaller);

let initialRole = 'public';

const userInfo = JSON.parse(localStorage.getItem('userInfo'));
if (userInfo && userInfo.rights) {
  initialRole = userInfo.rights;
}
// console.log(initialRole)
export default new AclCreate({
  initial: initialRole,
  notfound: '/pages/not-authorized',
  router,
  acceptLocalRules: true,
  globalRules: {
    super: new AclRule('super').generate(),
    admin: new AclRule('admin').generate(),
    user: new AclRule('user').generate(),
    public: new AclRule('public').or('user').or('admin').or('super').generate(),
  },
//   middleware: async acl => {
//     // await timeout(2000) // call your api
//     acl.change(initialRole)
//     console.log(initialRole)
//   }
});
