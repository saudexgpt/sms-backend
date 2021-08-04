/** When your routing table is too long, you can split it into small modules**/
const inventoryRoutes = {
  path: '/inventory',
  component: () => import('@/layouts/main/Main.vue'),
  name: 'Inventory',
  icon: 'DatabaseIcon',
  i18n: 'Inventory',
  slug: 'inventory',
  redirect: '/inventory/view',
  roles: ['admin', 'super'],
  children: [
    {
      hidden: false,
      path: '/inventory/view',
      name: 'ViewInventory',
      slug: 'view-inventory',
      i18n: 'View Inventory',
      component: () => import('@/views/apps/inventories'),
    },
    {
      hidden: false,
      component: () => import('@/views/apps/inventories/AddInventory'),
      path: '/inventory/add',
      name: 'AddInventory',
      slug: 'add-inventory',
      i18n: 'Add Inventory',
      meta: {
        // permissions: ['create-users', 'read-users', 'update-users', 'delete-users'],
      },
    },
    // {
    //   hidden: true,
    //   path: '/inventory/details/:id(\\d+)',
    //   name: 'InventoryDetails',
    //   slug: 'inventory-details',
    //   i18n: 'Inventory Details',
    //   component: () => import('@/views/apps/customers/Details'),
    // },
  ],
};

export default inventoryRoutes;
