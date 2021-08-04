<template>
  <div v-loading="load_table" v-if="page==='list'">
    <div class="vx-row">
      <div class="vx-col lg:w-3/4 w-full">
        <div class="flex items-end px-3">
          <feather-icon svg-classes="w-6 h-6" icon="ShoppingBagIcon" class="mr-2" />
          <span class="font-medium text-lg">Inventory of Products {{ sub_title }}</span>
        </div>
        <vs-divider />
      </div>
      <div class="vx-col lg:w-1/4 w-full">
        <div class="flex items-end px-3">
          <span class="pull-right">
            <el-select
              v-model="selected_staff_index"
              placeholder="Select Staff"
              clearable
              style="width: 100%"
              class="filter-item"
              filterable
              @change="viewByStaff"
            >
              <el-option
                v-for="(rep, index) in sales_reps"
                :key="index"
                :label="rep.name"
                :value="index"
              />
            </el-select>
            <!-- <el-button
                :loading="downloading"
                round
                class="filter-item"
                type="primary"
                icon="el-icon-download"
                @click="handleDownload"
              >Export</el-button> -->
          </span>
        </div>
      </div>
    </div>
    <v-client-table
      v-model="list"
      :columns="columns"
      :options="options"
    >
      <template slot="total_stocked" slot-scope="scope">
        <span>{{ scope.row.total_stocked + ' ' + scope.row.item.package_type }}</span>
      </template>
      <template slot="total_sold" slot-scope="scope">
        <span>{{ scope.row.total_sold + ' ' + scope.row.item.package_type }}</span>
      </template>
      <template slot="total_balance" slot-scope="scope">
        <span>{{ scope.row.total_balance + ' ' + scope.row.item.package_type }}</span>
      </template>
      <template slot="action" slot-scope="scope">
        <el-tooltip
          class="item"
          effect="dark"
          content="View Stock Details"
          placement="top-start"
        >
          <vs-button radius color="dark" type="filled" icon-pack="feather" icon="icon-eye" @click="showInventoryDetails(scope.row)"/>
          <!-- <el-button
            round
            type="success"
            size="small"
            icon="el-icon-view"
            @click="showInventoryDetails(scope.row)"
          /> -->
        </el-tooltip>
      </template>
    </v-client-table>
    <vs-popup :active.sync="popupActive" :title="details_title">
      <inventory-detail v-if="popupActive" :selected-item="selected_detail_item" />
    </vs-popup>
  </div>
</template>

<script>
import moment from 'moment';
import InventoryDetail from './InventoryDetail'; // Secondary package based on el-pagination
import Resource from '@/api/resource';
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission'; // Permission checking
const salesRepResource = new Resource('users/fetch-sales-reps');
const staffResource = new Resource('inventory/view-by-staff');
export default {
  name: 'Customers',
  components: { InventoryDetail },
  directives: { permission },
  data() {
    return {
      sales_reps: [],
      selected_staff_index: '',
      sub_title: '',
      list: [],
      columns: [
        'item.name',
        'total_stocked',
        'total_sold',
        'total_balance',
        'action',
      ],

      options: {
        headings: {
          'item.name': 'Product',
          total_stocked: 'Total Stocked',
          total_sold: 'Total Sold',
          total_balance: 'Total Balance',
        },
        pagination: {
          dropdown: true,
          chunk: 10,
        },
        perPage: 10,
        filterByColumn: true,
        // texts: {
        //   filter: 'Search:',
        // },
        // editableColumns:['name', 'category.name', 'sku'],
        sortable: ['item.name'],
        filterable: ['item.name'],
      },
      page: 'list',
      popupActive: false,
      details_title: '',
      selected_detail_item: '',
    };
  },
  created() {
    this.fetchSalesReps();
  },
  methods: {
    moment,
    checkPermission,
    showInventoryDetails(selected_item) {
      const app = this;
      app.details_title = 'Stock Details for ' + selected_item.item.name;
      app.selected_detail_item = selected_item;
      app.popupActive = true;
    },
    fetchSalesReps() {
      this.load_table = true;
      salesRepResource
        .list()
        .then((response) => {
          this.sales_reps = response.sales_reps;
          this.load_table = false;
        })
        .catch((error) => {
          console.log(error);
          this.load_table = false;
        });
    },
    viewByStaff() {
      const app = this;
      const staff = app.sales_reps[app.selected_staff_index];
      const param = { staff_id: staff.id };
      app.sub_title = 'for ' + staff.name;
      app.$vs.loading();
      staffResource
        .list(param)
        .then((response) => {
          app.list = response.inventories;
          app.$vs.loading.close();
        })
        .catch((error) => {
          console.log(error);
          app.$vs.loading.close();
        });
    },
    handleDownload(){
      const app = this;
      app.export(app.list);
    //   const param = { staff_id: app.selected_staff.id };
    //   this.downloading = true;
    //   staffResource.list(param)
    //     .then(response => {
    //       this.export(response.data);

    //       this.downloading = false;
    //     });
    },
    export(export_data) {
      import('@/vendor/Export2Excel').then((excel) => {
        const tHeader = [
          'PRODUCT',
          'TOTAL STOCKED',
          'TOTAL SOLD',
          'TOTAL BALANCE',
        ];
        const filterVal = [
          'item.name',
          'total_stocked',
          'total_sold',
          'total_balance',
        ];
        const data = this.formatJson(filterVal, export_data);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'inventory-by-staff',
        });
        this.downloading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map((v) =>
        filterVal.map((j) => {
          if (j === 'item.name') {
            return v['item']['name'];
          }
          return v[j];
        }),
      );
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
