<!-- =========================================================================================
    File Name: DashboardEcommerce.vue
    Description: Dashboard - Ecommerce
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
  <div>
    <div class="vx-row">

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line
          v-if="revenueGenerated.analyticsData"
          :statistic="revenueGenerated.analyticsData.revenue | k_formatter"
          :chart-data="revenueGenerated.series"
          icon="DollarSignIcon"
          statistic-title="Wallet"
          color="success"
          type="area" />
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line
          v-if="subscribersGained.analyticsData"
          :statistic="subscribersGained.analyticsData.subscribers | k_formatter"
          :chart-data="subscribersGained.series"
          icon="ShoppingCartIcon"
          statistic-title="Orders"
          color="dark"
          type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line
          v-if="quarterlySales.analyticsData"
          :statistic="quarterlySales.analyticsData.sales"
          :chart-data="quarterlySales.series"
          icon="UsersIcon"
          statistic-title="Downlines"
          color="danger"
          type="area" />
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line
          v-if="ordersRecevied.analyticsData"
          :statistic="ordersRecevied.analyticsData.orders | k_formatter"
          :chart-data="ordersRecevied.series"
          icon="Share2Icon"
          statistic-title="Referrals"
          color="warning"
          type="area" />
      </div>
    </div>

    <div class="vx-row">
      <!-- CARD 9: DISPATCHED ORDERS -->
      <div class="vx-col w-full">
        <vx-card title="Dispatched Orders">
          <div slot="no-body" class="mt-4">
            <vs-table :data="dispatchedOrders" class="table-dark-inverted">
              <template slot="thead">
                <vs-th>ORDER NO.</vs-th>
                <vs-th>STATUS</vs-th>
                <!-- <vs-th>OPERATORS</vs-th> -->
                <vs-th>LOCATION</vs-th>
                <vs-th>DISTANCE</vs-th>
                <vs-th>START DATE</vs-th>
                <vs-th>EST DELIVERY DATE</vs-th>
              </template>

              <template slot-scope="{data}">
                <vs-tr v-for="(tr, indextr) in data" :key="indextr">
                  <vs-td :data="data[indextr].orderNo">
                    <span>#{{ data[indextr].orderNo }}</span>
                  </vs-td>
                  <vs-td :data="data[indextr].status">
                    <span class="flex items-center px-2 py-1 rounded"><div :class="'bg-' + data[indextr].statusColor" class="h-3 w-3 rounded-full mr-2"/>{{ data[indextr].status }}</span>
                  </vs-td>
                  <!-- <vs-td :data="data[indextr].orderNo">
                    <ul class="users-liked user-list">
                      <li v-for="(user, userIndex) in data[indextr].usersLiked" :key="userIndex">
                        <vx-tooltip :text="user.name" position="bottom">
                          <vs-avatar :src="user.img" size="30px" class="border-2 border-white border-solid -m-1"/>
                        </vx-tooltip>
                      </li>
                    </ul>
                  </vs-td> -->
                  <vs-td :data="data[indextr].orderNo">
                    <span>{{ data[indextr].location }}</span>
                  </vs-td>
                  <vs-td :data="data[indextr].orderNo">
                    <span>{{ data[indextr].distance }}</span>
                    <vs-progress :percent="data[indextr].distPercent" :color="data[indextr].statusColor"/>
                  </vs-td>
                  <vs-td :data="data[indextr].orderNo">
                    <span>{{ data[indextr].startDate }}</span>
                  </vs-td>
                  <vs-td :data="data[indextr].orderNo">
                    <span>{{ data[indextr].estDelDate }}</span>
                  </vs-td>
                </vs-tr>
              </template>
            </vs-table>
          </div>

        </vx-card>
      </div>
    </div>
  </div>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import VueApexCharts from 'vue-apexcharts';
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue';
// import analyticsData from '@/views/ui-elements/card/analyticsData.js';
import ChangeTimeDurationDropdown from '@/components/ChangeTimeDurationDropdown.vue';

export default{
  components: {
    VueApexCharts,
    StatisticsCardLine,
    VuePerfectScrollbar,
    ChangeTimeDurationDropdown,
  },
  data() {
    return {
      subscribersGained: {
        series: [
          {
            name: 'Subscribers',
            data: [28, 40, 36, 52, 38, 60, 55],
          },
        ],
        analyticsData: {
          subscribers: 92600,
        },
      },
      revenueGenerated: {
        series: [
          {
            name: 'Revenue',
            data: [350, 275, 400, 300, 350, 300, 450],
          },
        ],
        analyticsData: {
          revenue: 97500,
        },
      },
      quarterlySales: {
        series: [
          {
            name: 'Sales',
            data: [10, 15, 7, 12, 3, 16],
          },
        ],
        analyticsData: {
          sales: '14',
        },
      },
      ordersRecevied: {
        series: [
          {
            name: 'Orders',
            data: [10, 15, 8, 15, 7, 12, 8],
          },
        ],
        analyticsData: {
          orders: 97500,
        },
      },

    //   dispatchedOrders: [
    //     {
    //       orderNo: 879985,
    //       status: 'Moving',
    //       statusColor: 'success',
    //       operator: 'Cinar Knowles',
    //       operatorImg: require('@/assets/images/portrait/small/avatar-s-2.jpg'),
    //       usersLiked: [
    //         {
    //           name: 'Vennie Mostowy',
    //           img: require('@/assets/images/portrait/small/avatar-s-5.jpg'),
    //         },
    //         {
    //           name: 'Elicia Rieske',
    //           img: require('@/assets/images/portrait/small/avatar-s-7.jpg'),
    //         },
    //         {
    //           name: 'Julee Rossignol',
    //           img: require('@/assets/images/portrait/small/avatar-s-10.jpg'),
    //         },
    //         {
    //           name: 'Darcey Nooner',
    //           img: require('@/assets/images/portrait/small/avatar-s-8.jpg'),
    //         },
    //       ],
    //       location: 'Anniston, Alabama',
    //       distance: '130 km',
    //       distPercent: 80,
    //       startDate: '26/07/2018',
    //       estDelDate: '28/07/2018',
    //     },
    //     {
    //       orderNo: 156897,
    //       status: 'Pending',
    //       statusColor: 'warning',
    //       operator: 'Britany Ryder',
    //       operatorImg: require('@/assets/images/portrait/small/avatar-s-4.jpg'),
    //       usersLiked: [
    //         {
    //           name: 'Trina Lynes',
    //           img: require('@/assets/images/portrait/small/avatar-s-1.jpg'),
    //         },
    //         {
    //           name: 'Lilian Nenez',
    //           img: require('@/assets/images/portrait/small/avatar-s-2.jpg'),
    //         },
    //         {
    //           name: 'Alberto Glotzbach',
    //           img: require('@/assets/images/portrait/small/avatar-s-3.jpg'),
    //         },
    //       ],
    //       location: 'Cordova, Alaska',
    //       distance: '234 km',
    //       distPercent: 60,
    //       startDate: '26/07/2018',
    //       estDelDate: '28/07/2018',
    //     },
    //     {
    //       orderNo: 568975,
    //       status: 'Moving',
    //       statusColor: 'success',
    //       operator: 'Kishan Ashton',
    //       operatorImg: require('@/assets/images/portrait/small/avatar-s-1.jpg'),
    //       usersLiked: [
    //         {
    //           name: 'Lai Lewandowski',
    //           img: require('@/assets/images/portrait/small/avatar-s-6.jpg'),
    //         },
    //         {
    //           name: 'Elicia Rieske',
    //           img: require('@/assets/images/portrait/small/avatar-s-7.jpg'),
    //         },
    //         {
    //           name: 'Darcey Nooner',
    //           img: require('@/assets/images/portrait/small/avatar-s-8.jpg'),
    //         },
    //         {
    //           name: 'Julee Rossignol',
    //           img: require('@/assets/images/portrait/small/avatar-s-10.jpg'),
    //         },
    //         {
    //           name: 'Jeffrey Gerondale',
    //           img: require('@/assets/images/portrait/small/avatar-s-9.jpg'),
    //         },
    //       ],
    //       location: 'Florence, Alabama',
    //       distance: '168 km',
    //       distPercent: 70,
    //       startDate: '26/07/2018',
    //       estDelDate: '28/07/2018',
    //     },
    //     {
    //       orderNo: 245689,
    //       status: 'Canceled',
    //       statusColor: 'danger',
    //       operator: 'Anabella Elliott',
    //       operatorImg: require('@/assets/images/portrait/small/avatar-s-6.jpg'),
    //       usersLiked: [
    //         {
    //           name: 'Vennie Mostowy',
    //           img: require('@/assets/images/portrait/small/avatar-s-5.jpg'),
    //         },
    //         {
    //           name: 'Elicia Rieske',
    //           img: require('@/assets/images/portrait/small/avatar-s-7.jpg'),
    //         },
    //       ],
    //       location: 'Clifton, Arizona',
    //       distance: '125 km',
    //       distPercent: 95,
    //       startDate: '26/07/2018',
    //       estDelDate: '28/07/2018',
    //     },
    //   ],
    };
  },
  computed: {
    scrollbarTag() {
      return this.$store.getters.scrollbarTag;
    },
  },
  mounted() {
    // const scroll_el = this.$refs.chatLogPS.$el || this.$refs.chatLogPS;
    // scroll_el.scrollTop = this.$refs.chatLog.scrollHeight;
  },
  created() {

  },
};
</script>

<style lang="scss">
.chat-card-log {
    height: 400px;

    .chat-sent-msg {
        background-color: #f2f4f7 !important;
    }
}
</style>
