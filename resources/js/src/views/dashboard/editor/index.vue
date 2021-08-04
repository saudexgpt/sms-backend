<!-- =========================================================================================
  File Name: DashboardAnalytics.vue
  Description: Dashboard Analytics
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
  <div id="dashboard-analytics">
    <div class="vx-row"/>
    <div class="vx-row">

      <!-- CARD 1: CONGRATS -->
      <div class="vx-col w-full lg:w-3/5 mb-base">
        <div class="vx-row">
          <div class="vx-col w-full mb-base">
            <vx-card slot="no-body" class="text-center bg-dark-gradient greet-user">
              <img src="@assets/images/elements/decore-left.png" class="decore-left" alt="Decore Left" width="200" >
              <img src="@assets/images/elements/decore-right.png" class="decore-right" alt="Decore Right" width="175">
              <feather-icon icon="AwardIcon" class="p-6 mb-8 bg-primary inline-flex rounded-full text-white shadow" svg-classes="h-8 w-8"/>
              <h3 class="mb-6 text-white">Welcome {{ user }},</h3>
              <p class="xl:w-3/4 lg:w-4/5 md:w-2/3 w-4/5 mx-auto text-white"> <strong>Membership Id (HWB0001008) Stage: 0</strong></p>
            </vx-card>
          </div>
        </div>
        <div class="vx-row">
          <!-- CARD 6: Product Orders -->
          <div class="vx-col w-full lg:w-3/5 mb-base">
            <vx-card title="Product Orders">
              <!-- CARD ACTION -->
              <template slot="actions">
                <change-time-duration-dropdown />
              </template>

              <!-- Chart -->
              <div slot="no-body">
                <vue-apex-charts :options="analyticsData.productOrdersRadialBar.chartOptions" :series="productsOrder.series" type="radialBar" height="420" />
              </div>

              <ul>
                <li v-for="orderData in productsOrder.analyticsData" :key="orderData.orderType" class="flex mb-3 justify-between">
                  <span class="flex items-center">
                    <span :class="`border-${orderData.color}`" class="inline-block h-4 w-4 rounded-full mr-2 bg-white border-3 border-solid"/>
                    <span class="font-semibold">{{ orderData.orderType }}</span>
                  </span>
                  <span>{{ orderData.counts }}</span>
                </li>
              </ul>
            </vx-card>
          </div>

          <!-- CARD 7: Sales Stats -->
          <div class="vx-col w-full lg:w-2/5 mb-base">
            <vx-card title="Sales Stats" subtitle="Last 6 Months">
              <template slot="actions">
                <feather-icon icon="MoreVerticalIcon" svg-classes="w-6 h-6 text-grey"/>
              </template>
              <div class="flex">
                <span class="flex items-center"><div class="h-3 w-3 rounded-full mr-1 bg-primary"/><span>Sales</span></span>
                <span class="flex items-center ml-4"><div class="h-3 w-3 rounded-full mr-1 bg-success"/><span>Visits</span></span>
              </div>
              <div slot="no-body-bottom">
                <vue-apex-charts :options="analyticsData.statisticsRadar.chartOptions" :series="salesRadar.series" type="radar" height="385" />
              </div>
            </vx-card>
          </div>
        </div>
      </div>
      <div class="vx-col w-full lg:w-2/5 mb-base">
        <div class="vx-row">
          <div class="vx-col w-full md:w-1/2 mb-base">
            <statistics-card-line :chart-data="subscribersGained.series" icon="DollarSignIcon" statistic="92.6k" statistic-title="Wallet" type="area" color="success" />
          </div>

          <!-- CARD 3: ORDER RECIEVED -->
          <div class="vx-col w-full md:w-1/2 mb-base">
            <statistics-card-line :chart-data="ordersRecevied.series" icon="ShoppingCartIcon" statistic="97.5K" statistic-title="Orders" color="dark" type="area"/>
          </div>
          <div class="vx-col w-full md:w-1/2 mb-base">
            <statistics-card-line :chart-data="ordersRecevied.series" icon="UsersIcon" statistic="97.5K" statistic-title="Downlines" color="warning" type="area"/>
          </div>
          <div class="vx-col w-full md:w-1/2 mb-base">
            <statistics-card-line :chart-data="ordersRecevied.series" icon="Share2Icon" statistic="97.5K" statistic-title="Referrals" color="danger" type="area"/>
          </div>
          <div class="vx-col w-full mb-base">
            <statistics-card-line :chart-data="ordersRecevied.series" icon="RepeatIcon" statistic="97.5K" statistic-title="Transactions" color="info" type="area"/>
          </div>
        </div>
      </div>

      <!-- CARD 2: SUBSCRIBERS GAINED -->

    </div>

    <!-- <div class="vx-row">
      <div class="vx-col w-full">
        <vx-card title="Dispatched Orders">
          <div slot="no-body" class="mt-4">
            <vs-table :data="dispatchedOrders" class="table-dark-inverted">
              <template slot="thead">
                <vs-th>ORDER NO.</vs-th>
                <vs-th>STATUS</vs-th>
                <vs-th>OPERATORS</vs-th>
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
                  <vs-td :data="data[indextr].orderNo">
                    <ul class="users-liked user-list">
                      <li v-for="(user, userIndex) in data[indextr].usersLiked" :key="userIndex">
                        <vx-tooltip :text="user.name" position="bottom">
                          <vs-avatar :src="user.img" size="30px" class="border-2 border-white border-solid -m-1"/>
                        </vx-tooltip>
                      </li>
                    </ul>
                  </vs-td>
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
    </div> -->

  </div>
</template>

<script>
import VueApexCharts from 'vue-apexcharts';
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue';
import analyticsData from '@/views/ui-elements/card/analyticsData.js';
import ChangeTimeDurationDropdown from '@/components/ChangeTimeDurationDropdown.vue';
import VxTimeline from '@/components/timeline/VxTimeline';
// import Resource from '@/api/resource';
export default {
  components: {
    VueApexCharts,
    StatisticsCardLine,
    ChangeTimeDurationDropdown,
    VxTimeline,
  },
  data() {
    return {
      subscribersGained: {
        analyticsData: [
          {
            device: 'Dekstop',
            icon: 'MonitorIcon',
            color: 'primary',
            sessionsPercentage: 58.6,
            comparedResultPercentage: 2,
          },
          {
            device: 'Mobile',
            icon: 'SmartphoneIcon',
            color: 'warning',
            sessionsPercentage: 34.9,
            comparedResultPercentage: 8,
          },
          {
            device: 'Tablet',
            icon: 'TabletIcon',
            color: 'danger',
            sessionsPercentage: 6.5,
            comparedResultPercentage: -5,
          },
        ],
        series: [58.6, 34.9, 6.5],
      },
      productsOrder: {
        analyticsData: [
          {
            'orderType': 'Finished',
            'counts': 23043,
            'color': 'primary',
          },
          {
            'orderType': 'Pending',
            'counts': 14658,
            'color': 'warning',
          },
          {
            'orderType': 'Rejected ',
            'counts': 4758,
            'color': 'danger',
          },
        ],
        series: [70, 52, 26],
      },
      customers: {
        analyticsData: [
          {
            'customerType': 'New',
            'counts': 890,
            'color': 'primary',
          },
          {
            'customerType': 'Returning',
            'counts': 258,
            'color': 'warning',
          },
          {
            'customerType': 'Referrals ',
            'counts': 149,
            'color': 'danger',
          },
        ],
        series: [690, 258, 149],
      },
      salesRadar: {
        series: [
          {
            name: 'Visits',
            data: [90, 50, 86, 40, 100, 20],
          },
          {
            name: 'Sales',
            data: [70, 75, 70, 76, 20, 85],
          },
        ],
      },
      supportTracker: {
        analyticsData: {
          openTickets: 163,
          meta: {
            'New Tickets': 29,
            'Open Tickets': 63,
            'Response Time': '1d',
          },
        },
        series: [83],
      },
      revenueComparisonLine: {
        analyticsData: {
          thisMonth: 86589,
          lastMonth: 73683,
        },
        series: [
          {
            name: 'This Month',
            data: [45000, 47000, 44800, 47500, 45500, 48000, 46500, 48600],
          },
          {
            name: 'Last Month',
            data: [46000, 48000, 45500, 46600, 44500, 46500, 45000, 47000],
          },
        ],
      },
      goalOverviewRadialBar: {
        analyticsData: {
          completed: 786617,
          inProgress: 13561,
        },
        series: [83],
      },
      salesBarSession: {
        series: [
          {
            name: 'Sessions',
            data: [75, 125, 225, 175, 125, 75, 25],
          },
        ],
        analyticsData: {
          session: 2700,
          comparison: {
            str: 'Last 7 Days',
            result: +5.2,
          },
        },
      },
      todoToday: {
        date: 'Sat, 16 Feb',
        numCompletedTasks: 2,
        totalTasks: 10,
        tasksToday: [
          {
            id: 3,
            task: 'Refactor button component',
            date: '16 Feb 2019',
          },
          {
            id: 70,
            task: 'Submit report to admin',
            date: '16 Feb 2019',
          },
          {
            id: 8,
            task: 'Prepare presentation',
            date: '16 Feb 2019',
          },
          {
            id: 1,
            task: 'Calculate monthly income',
            date: '16 Feb 2019',
          },
        ],
      },
      salesLine: {
        series: [
          {
            name: 'Sales',
            data: [140, 180, 150, 205, 160, 295, 125, 255, 205, 305, 240, 295],
          },
        ],
      },
      funding: {
        currBalance: 22597,
        depostis: 20065,
        comparison: {
          resultPerc: 5.2,
          pastData: 956,
        },
        meta: {
          earned: {
            val: 56156,
            progress: 50,
          },
          duration: {
            val: '2 Year',
            progress: 50,
          },
        },
      },
      browserAnalytics: [
        {
          id: 1,
          name: 'Google Chrome',
          ratio: 73,
          time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
          comparedResult: '800',
        },
        {
          id: 3,
          name: 'Opera',
          ratio: 8,
          time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
          comparedResult: '-200',
        },
        {
          id: 2,
          name: 'Firefox',
          ratio: 19,
          time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
          comparedResult: '100',
        },
        {
          id: 4,
          name: 'Internet Explorer',
          ratio: 27,
          time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
          comparedResult: '-450',
        },
      ],
      clientRetention: {
        series: [
          {
            name: 'New Clients',
            data: [175, 125, 225, 175, 160, 189, 206, 134, 159, 216, 148, 123],
          },
          {
            name: 'Retained Clients',
            data: [-144, -155, -141, -167, -122, -143, -158, -107, -126, -131, -140, -137],
          },
        ],
      },
      checkpointReward: {},
      ordersRecevied: {},

      timelineData: [
        {
          color: 'primary',
          icon: 'PlusIcon',
          title: 'Client Meeting',
          desc: 'Bonbon macaroon jelly beans gummi bears jelly lollipop apple',
          time: '25 mins Ago',
        },
        {
          color: 'warning',
          icon: 'MailIcon',
          title: 'Email Newsletter',
          desc: 'Cupcake gummi bears soufflé caramels candy',
          time: '15 Days Ago',
        },
        {
          color: 'danger',
          icon: 'UsersIcon',
          title: 'Plan Webinar',
          desc: 'Candy ice cream cake. Halvah gummi bears',
          time: '20 days ago',
        },
        {
          color: 'success',
          icon: 'LayoutIcon',
          title: 'Launch Website',
          desc: 'Candy ice cream cake. Halvah gummi bears Cupcake gummi bears soufflé caramels candy.',
          time: '25 days ago',
        },
        {
          color: 'primary',
          icon: 'TvIcon',
          title: 'Marketing',
          desc: 'Candy ice cream cake. Halvah gummi bears Cupcake gummi bears.',
          time: '28 days ago',
        },
      ],
      user: '',
      analyticsData,
      dispatchedOrders: [],
    };
  },
  created() {
    this.user = this.$store.getters.name;
  },
  methods: {

  },
};
</script>

<style lang="scss">
/*! rtl:begin:ignore */
#dashboard-analytics {
  .greet-user{
    position: relative;

    .decore-left{
      position: absolute;
      left:0;
      top: 0;
    }
    .decore-right{
      position: absolute;
      right:0;
      top: 0;
    }
  }

  @media(max-width: 576px) {
    .decore-left, .decore-right{
      width: 140px;
    }
  }
}
/*! rtl:end:ignore */
</style>
