<template>
  <div>
    <v-client-table
      v-model="details"
      :columns="columns"
      :options="options"
    >
      <template slot="quantity_stocked" slot-scope="scope">
        <span>{{ scope.row.quantity_stocked + ' ' + scope.row.item.package_type }}</span>
      </template>
      <template slot="sold" slot-scope="scope">
        <span>{{ scope.row.sold + ' ' + scope.row.item.package_type }}</span>
      </template>
      <template slot="balance" slot-scope="scope">
        <span>{{ scope.row.balance + ' ' + scope.row.item.package_type }}</span>
      </template>
    </v-client-table>
  </div>
</template>

<script>
import moment from 'moment';
import Resource from '@/api/resource';
const detailsResource = new Resource('inventory/view-details');
export default {
  name: 'Details',
  props: {
    selectedItem: {
      type: Object,
      default: () => (null),
    },
  },
  data() {
    return {
      details: [],
      columns: [
        // 'item.name',
        'quantity_stocked',
        'sold',
        'balance',
        // 'action',
      ],

      options: {
        headings: {
          // 'item.name': 'Product',
          quantity_stocked: 'Quantity Stocked',
          sold: 'Sold',
          balance: 'Balance',
        },
        pagination: {
          dropdown: true,
          chunk: 10,
        },
        perPage: 10,
        filterByColumn: false,
        // texts: {
        //   filter: 'Search:',
        // },
        // editableColumns:['name', 'category.name', 'sku'],
        sortable: [''],
        filterable: [''],
      },
      page: 'details',
      popupActive: false,
      details_title: '',
    };
  },
  created() {
    this.fetchDetails();
  },
  methods: {
    moment,
    fetchDetails() {
      const app = this;
      const param = { item_id: app.selectedItem.item_id, staff_id: app.selectedItem.staff_id };
      app.$vs.loading();
      detailsResource
        .list(param)
        .then((response) => {
          app.details = response.inventories;
          app.$vs.loading.close();
        })
        .catch((error) => {
          console.log(error);
          app.$vs.loading.close();
        });
    },
  },
};
</script>
<style>
.vs-con-input {
    margin-top: 20px !important ;
}
.dialog-footer {
    background: #f0f0f0;
    padding: 10px;
    margin-top: 20px !important ;
    position: relative;
}
</style>
