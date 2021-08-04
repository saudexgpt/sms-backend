<template>
  <div>
    <vx-card v-loading="load_table">
      <div class="vx-row">
        <div class="vx-col lg:w-4/5 w-full">
          <div class="flex items-end px-3">
            <feather-icon svg-classes="w-6 h-6" icon="ShoppingBagIcon" class="mr-2" />
            <span class="font-medium text-lg">Create New Inventory</span>
          </div>
          <vs-divider />
        </div>
        <div class="vx-col lg:w-1/5 w-full">
          <div class="flex items-end px-3">
            <span class="vx-col flex-1">
              <router-link
                :to="'/inventory/view'"
              >
                <el-button
                  round
                  class="filter-item"
                  type="primary"
                  icon="el-icon-s-goods"
                >View Inventory
                </el-button>
              </router-link>
            </span>
          </div>
        </div>
      </div>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th />
            <th>Choose Staff</th>
            <th>Choose Product</th>
            <th>Quantity to stock</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(inventory_item, index) in inventory_items" :key="index">
            <td>
              <span>
                <vs-button radius color="danger" type="filled" icon-pack="feather" icon="icon-trash-2" @click="removeLine(index)"/>
                <vs-button v-if="index + 1 === inventory_items.length" radius color="success" type="filled" icon-pack="feather" icon="icon-plus" @click="addLine(index)"/>
              </span>
            </td>
            <td>
              <el-select
                v-model="inventory_item.staff_id"
                placeholder="Select Staff"
                filterable
                class="span"
                style="width: 100%"
              >
                <el-option
                  v-for="(staff, staff_index) in sales_reps"
                  :key="staff_index"
                  :value="staff.id"
                  :label="staff.name"
                />
              </el-select>
            </td>
            <td>
              <el-select
                v-model="inventory_item.item_index"
                placeholder="Select Product"
                filterable
                class="span"
                style="width: 100%"
                @input="fetchItemDetails(index)"
              >
                <el-option
                  v-for="(item, item_index) in products"
                  :key="item_index"
                  :value="item_index"
                  :label="item.name"
                />
              </el-select>
            </td>
            <td>
              <el-input
                v-model="inventory_item.quantity"
                type="number"
                outline
                placeholder="Quantity"
                min="1">
                <template slot="append">{{ inventory_item.type }} </template>
              </el-input>
            </td>
          </tr>
          <tr v-if="fill_fields_error">
            <td colspan="4">
              <label
                class="label label-danger"
              >Please fill all empty fields before adding another row</label>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="4">
              <span class="pull-right">
                <vs-button color="success" type="filled" @click="submitInventory()">Submit</vs-button>
              </span>
            </th>
          </tr>
        </tfoot>
      </table>
    </vx-card>
  </div>
</template>

<script>
import moment from 'moment';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Resource from '@/api/resource';
import permission from '@/directive/permission'; // Permission directive
import checkPermission from '@/utils/permission'; // Permission checking
const productsResource = new Resource('products');
const salesRepResource = new Resource('users/fetch-sales-reps');
const createInventory = new Resource('inventory/store');
export default {
  name: 'Customers',
  components: { Pagination },
  directives: { permission },
  data() {
    return {
      sales_reps: [],
      products: [],
      inventory_items: [],
      fill_fields_error: false,
    };
  },
  created() {
    this.fetchSalesReps();
    this.fetchProducts();
    this.addLine();
  },
  methods: {
    moment,
    checkPermission,
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
    fetchProducts() {
      productsResource
        .list()
        .then((response) => {
          this.products = response.items;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    addLine(index) {
      this.fill_fields_error = false;

      const checkEmptyLines = this.inventory_items.filter(
        (detail) =>
          detail.item_id === '' ||
          detail.staff_id === '' ||
          detail.quantity === ''
      );

      if (checkEmptyLines.length >= 1 && this.inventory_items.length > 0) {
        this.fill_fields_error = true;
        // this.inventory_items[index].seleted_category = true;
        return;
      } else {
        // if (this.inventory_items.length > 0)
        //     this.inventory_items[index].grade = '';

        this.inventory_items.push({
          item_index: null,
          item_id: '',
          quantity: 1,
          staff_id: '',
        });
      }
    },
    removeLine(detailId) {
      this.fill_fields_error = false;
      if (!this.blockRemoval) {
        this.inventory_items.splice(detailId, 1);
      }
    },
    fetchItemDetails(index) {
      const app = this;
      const item_index = app.inventory_items[index].item_index;
      const item = app.products[item_index];
      app.inventory_items[index].item_id = item.id;
      app.inventory_items[index].type = item.package_type;
    },
    submitInventory() {
      const app = this;
      var form = { inventory_items: app.inventory_items };
      this.$vs.loading();
      createInventory
        .store(form)
        .then((response) => {
          app.$message({
            message: 'Invoice Created Successfully!!!',
            type: 'success',
          });
          app.inventory_items = [];
          app.addLine();
          this.$vs.loading.close();
        })
        .catch((error) => {
          this.$vs.loading.close();
          console.log(error.message);
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
