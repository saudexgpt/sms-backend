<template>
  <div>
    <vx-card v-loading="load_table" v-if="page==='list'">
      <div class="vx-row">
        <div class="vx-col lg:w-3/4 w-full">
          <div class="flex items-end px-3">
            <feather-icon svg-classes="w-6 h-6" icon="UsersIcon" class="mr-2" />
            <span class="font-medium text-lg">List of Customers</span>
          </div>
          <vs-divider />
        </div>
        <div class="vx-col lg:w-1/4 w-full">
          <div class="flex items-end px-3">
            <span class="pull-right">
              <router-link
                :to="'/customers/map'"
              >
                <el-button
                  round
                  class="filter-item"
                  type="default"
                  icon="el-icon-map-location"
                >Map
                </el-button>
              </router-link>
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
        </div>
      </div>
      <div class="filter-container">
        <el-row :gutter="20">
          <el-col :xs="24" :sm="12" :md="12">
            <el-input
              v-model="query.keyword"
              placeholder="Search User"
              style="width: 200px"
              class="filter-item"
              @input="handleFilter"
            />
          </el-col>
        </el-row>
      </div>
      <v-client-table
        v-if="list.length > 0"
        v-model="list"
        :columns="columns"
        :options="options"
      >
        <template slot="visits" slot-scope="scope">
          <span>{{ (scope.row.visits.length > 0) ? moment(scope.row.visits[0].visit_date).format('ll') : '' }}</span>
        </template>
        <template slot="created_at" slot-scope="scope">
          <span>{{ moment(scope.row.created_at).format('ll') }}</span>
        </template>
        <template slot="date_verified" slot-scope="scope">
          <span>{{ (scope.row.date_verified) ? moment(scope.row.date_verified).format('ll') : '' }}</span>
        </template>
        <template slot="action" slot-scope="scope">
          <el-tooltip
            class="item"
            effect="dark"
            content="View Customer Details"
            placement="top-start"
          >
            <router-link
              :to="'/customer/details/' + scope.row.id"
            >
              <el-button
                round
                type="success"
                size="small"
                icon="el-icon-view"
              />
            </router-link>
          </el-tooltip>
          <el-tooltip
            class="item"
            effect="dark"
            content="Edit User"
            placement="top-start"
          >
            <router-link
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
          </el-tooltip>
          <el-tooltip
            class="item"
            effect="dark"
            content="Delete User"
            placement="top-start"
          >
            <el-button
              v-permission="['delete-users']"
              round
              type="danger"
              size="small"
              icon="el-icon-delete"
              @click="handleDelete(scope.index, scope.row.id, scope.row.business_name)"
            />
          </el-tooltip>
        </template>
      </v-client-table>
      <el-row :gutter="20">
        <pagination
          v-show="total > 0"
          :total="total"
          :page.sync="query.page"
          :limit.sync="query.limit"
          @pagination="getList"
        />
      </el-row>

      <!-- <vs-popup
        :active.sync="dialogFormVisible"
        fullscreen
        title="Add New User">
        <div v-loading="userCreating" class="con-exemple-prompt">
          <form >
            <div class="vx-row">
              <div class="vx-col sm:w-1/2 w-full mb-2">
                <vs-input v-model="newCustomer.first_name" v-validate="'required'" name="first_name" label-placeholder="First Name" class="mt-3 w-full" data-vv-validate-on="blur"/>
                <span v-show="errors.has('first_name')" class="text-danger text-sm">{{ errors.first('first_name') }}</span>
              </div>
              <div class="vx-col sm:w-1/2 w-full mb-2">
                <vs-input v-model="newCustomer.last_name" v-validate="'required'" name="last_name" label-placeholder="Last Name" class="mt-3 w-full" data-vv-validate-on="blur"/>
                <span v-show="errors.has('last_name')" class="text-danger text-sm">{{ errors.first('last_name') }}</span>
              </div>
            </div>
            <div class="vx-row">
              <div class="vx-col sm:w-1/2 w-full mb-2">
                <vs-input v-model="newCustomer.email" v-validate="'required'" type="email" name="email" label-placeholder="Email" class="mt-3 w-full" data-vv-validate-on="blur"/>
                <span v-show="errors.has('email')" class="text-danger text-sm">{{ errors.first('email') }}</span>
              </div>
              <div class="vx-col sm:w-1/2 w-full mb-2">
                <vs-input v-model="newCustomer.username" v-validate="'required'" name="username" label-placeholder="Username" class="mt-3 w-full" data-vv-validate-on="blur"/>
                <span v-show="errors.has('username')" class="text-danger text-sm">{{ errors.first('username') }}</span>
              </div>
            </div>
            <div class="vx-row">
              <div class="vx-col sm:w-1/2 w-full mb-2">
                <vs-input v-model="newCustomer.password" v-validate="'required|min:8'" name="password" type="password" show-password label-placeholder="Password" class="mt-3 w-full" data-vv-validate-on="blur"/>
                <span v-show="errors.has('password')" class="text-danger text-sm">{{ errors.first('password') }}</span>
              </div>
              <div class="vx-col sm:w-1/2 w-full mb-2">
                <vs-input v-model="newCustomer.confirmPassword" v-validate="'required|min:8|confirmed:password'" name="confirm-password" type="password" show-password label-placeholder="Confirm Password" class="mt-3 w-full" data-vv-validate-on="blur"/>
                <span v-show="errors.has('confirm-password')" class="text-danger text-sm">{{ errors.first('confirm-password') }}</span>
              </div>
            </div>

            <div class="dialog-footer">
              <vs-button color="danger" type="filled" @click="dialogFormVisible = false">Cancel</vs-button>
              <vs-button color="success" type="filled" @click.prevent="createUser()">Submit</vs-button>
            </div>
          </form>
        </div>
      </vs-popup> -->
    </vx-card>
  </div>
</template>

<script>
import moment from 'moment';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Resource from '@/api/resource';
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission'; // Permission checking
const customersResource = new Resource('customers');
export default {
  name: 'Customers',
  components: { Pagination },
  directives: { permission },
  data() {
    return {
      list: [],
      columns: [
        'business_name',
        'customer_type.name',
        'area',
        'visits',
        'registrar.name',
        'assigned_officer.name',
        'verifier.name',
        'created_at',
        'date_verified',
        'action',
      ],

      options: {
        headings: {
          business_name: 'Name',
          'customer_type.name': 'Type',
          'visits': 'Last Visit',
          'registrar.name': 'Created By',
          'assigned_officer.name': 'Field Staff',
          'verifier.name': 'Verified By',
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
        sortable: ['created_at', 'date_verified'],
        filterable: ['business_name', 'customer_type.name', 'area', 'registrar.name', 'assigned_officer.name'],
      },
      total: 0,
      loading: false,
      load_table: false,
      downloading: false,
      userCreating: false,
      query: {
        page: 1,
        limit: 10,
        keyword: '',
        role: '',
      },
      newCustomer: {},
      dialogFormVisible: false,
      selected_customer: '',
      page: 'list',
    };
  },
  created() {
    this.getList();
  },
  methods: {
    moment,
    checkPermission,
    getList() {
      const { limit, page } = this.query;
      this.options.perPage = limit;
      this.load_table = true;
      customersResource
        .list(this.query)
        .then((response) => {
          const customers = response.customers;
          this.list = customers.data;
          this.list.forEach((element, index) => {
            element['index'] = (page - 1) * limit + index + 1;
          });
          this.total = customers.total;
          this.load_table = false;
        })
        .catch((error) => {
          console.log(error);
          this.load_table = false;
        });
    },
    showCustomerDetails(selectedCustomer){
      const app = this;
      app.selected_customer = selectedCustomer;
      app.page = 'details';
    },
    handleCreate() {
      this.resetNewUser();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$validator.reset();
      });
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    handleDownload(){
      // fetch all data for export
      this.query.limit = this.total;
      this.downloading = true;
      customersResource.list(this.query)
        .then(response => {
          this.export(response.data);

          this.downloading = false;
        });
    },
    export(export_data) {
      import('@/vendor/Export2Excel').then((excel) => {
        const tHeader = [
          'name',
          'email',
          'phone',
          'address',
        ];
        const filterVal = [
          'name',
          'email',
          'phone',
          'address',
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
