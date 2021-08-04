<template>
  <div class="app-container">

    <el-card v-loading="load_table" >
      <div slot="header" class="clearfix">
        <span>Revenue List</span>
        <span class="pull-right">
          <el-button
            :loading="downloading"
            round
            class="filter-item"
            type="primary"
            icon="el-icon-download"
            @click="handleDownload"
          >Export</el-button>
        </span>
      </div>
      <div class="filter-container">
        <el-input
          v-model="query.keyword"
          placeholder="Search"
          style="width: 200px"
          class="filter-item"
          @input="handleFilter"
        />
      </div>
      <v-client-table
        v-if="revenue.length > 0"
        v-model="revenue"
        :columns="columns"
        :options="options"
      >
        <template slot="action" slot-scope="scope">
          <!-- <el-tooltip
            class="item"
            effect="dark"
            content="Edit User"
            placement="top-start"
          >
            <router-link
              v-if="!scope.row.roles.includes('admin')"
              :to="'/administrator/users/edit/' + scope.row.id"
            >
              <el-button
                v-permission="['update-users']"
                round
                type="primary"
                size="small"
                icon="el-icon-edit"
              />
            </router-link>
          </el-tooltip> -->
        </template>
      </v-client-table>
    </el-card>

    <el-row>
      <pagination
        :total="total"
        :page.sync="query.page"
        :limit.sync="query.limit"
        @pagination="getRevenue"
      />
    </el-row>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Resource from '@/api/resource';
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission'; // Permission checking

const revenueResource = new Resource('transactions/revenue');
export default {
  name: 'ViewRevenue',
  components: { Pagination },
  directives: { permission },
  props: {
    canAddNew: {
      type: Boolean,
      default: () => true,
    },
  },
  data() {
    return {
      revenue: [],
      columns: [
        'action',
        'payment_ref',
        'payment_date',
        'phone_no',
        'receipt_no',
        'tin',
        'customer_name',
        'revenue_item',
        'amount',
        'payment_method',
        'deposit_slip',
        'cheque_value_date',
        'bank',
        'bank_branch',
        'payment_code',
        'settlement_date',
        'retrieval_ref_number',
      ],

      options: {
        headings: {
          action: 'Action',
          payment_ref: 'PAYMENT REF. NO.',
          payment_date: 'PAYMENT DATE',
          phone_no: 'PHONE NO.',
          receipt_no: 'RECEIPT NO.',
          tin: 'TIN',
          customer_name: 'CUSTOMER NAME',
          revenue_item: 'REVENUE ITEM',
          amount: 'AMOUNT',
          payment_method: 'PAYMENT METHOD',
          deposit_slip: 'DEPOSIT SLIP',
          cheque_value_date: 'CHEQUE VALUE DATE',
          bank: 'BANK',
          bank_branch: 'BANK BRANCH',
          payment_code: 'PAYMENT CODE',
          settlement_date: 'SETTLEMENT DATE',
          retrieval_ref_number: 'RETRIEVAL REF. NO.',
        },
        pagination: {
          dropdown: true,
          chunk: 100,
        },
        perPage: 100,
        filterByColumn: true,
        // texts: {
        //   filter: 'Search:',
        // },
        // editableColumns:['name', 'category.name', 'sku'],
        sortable: ['payment_date'],
        filterable: ['payment_ref', 'customer_name', 'phone_no', 'bank', 'tin'],
      },
      total: 0,
      loading: false,
      load_table: false,
      downloading: false,
      query: {
        page: 1,
        limit: 10,
        keyword: '',
        role: '',
      },
    };
  },
  created() {
    this.getRevenue();
  },
  methods: {
    checkPermission,
    getRevenue() {
      const { limit, page } = this.query;
      this.options.perPage = limit;
      this.load_table = true;
      revenueResource
        .list(this.query)
        .then((response) => {
          const transactions = response.transactions;
          this.revenue = transactions.data;
          this.revenue.forEach((element, index) => {
            element['index'] = (page - 1) * limit + index + 1;
          });
          this.total = transactions.total;
          this.load_table = false;
        })
        .catch((error) => {
          console.log(error);
          this.load_table = false;
        });
    },
    handleFilter() {
      this.query.page = 1;
      this.getRevenue();
    },
    handleDownload(){
      // fetch all data for export
      this.query.limit = this.total;
      this.downloading = true;
      revenueResource.list(this.query)
        .then(response => {
          const transactions = response.transactions;
          this.export(transactions.data);

          this.downloading = false;
        });
    },
    export(export_data) {
      import('@/vendor/Export2Excel').then((excel) => {
        const tHeader = [
          'payment_ref',
          'payment_date',
          'phone_no',
          'receipt_no',
          'tin',
          'customer_name',
          'revenue_item',
          'amount',
          'payment_method',
          'deposit_slip',
          'cheque_value_date',
          'bank',
          'bank_branch',
          'payment_code',
          'settlement_date',
          'retrieval_ref_number',
        ];
        const filterVal = [
          'payment_ref',
          'payment_date',
          'phone_no',
          'receipt_no',
          'tin',
          'customer_name',
          'revenue_item',
          'amount',
          'payment_method',
          'deposit_slip',
          'cheque_value_date',
          'bank',
          'bank_branch',
          'payment_code',
          'settlement_date',
          'retrieval_ref_number',
        ];
        const data = this.formatJson(filterVal, export_data);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'user-list',
        });
        this.downloading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map((v) =>
        filterVal.map((j) => {
          if (j === 'role') {
            return v['roles'].join(', ');
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
