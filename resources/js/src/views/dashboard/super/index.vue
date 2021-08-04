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
          v-if="subscribersGained.analyticsData"
          :statistic="subscribersGained.analyticsData.subscribers | k_formatter"
          :chart-data="subscribersGained.series"
          icon="UsersIcon"
          statistic-title="Members"
          color="dark"
          type="area" />
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line
          v-if="quarterlySales.analyticsData"
          :statistic="quarterlySales.analyticsData.sales"
          :chart-data="quarterlySales.series"
          icon="ShoppingCartIcon"
          statistic-title="Quarterly Sales"
          color="danger"
          type="area" />
      </div>
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line
          v-if="revenueGenerated.analyticsData"
          :statistic="revenueGenerated.analyticsData.revenue | k_formatter"
          :chart-data="revenueGenerated.series"
          icon="DollarSignIcon"
          statistic-title="Revenue Generated"
          color="success"
          type="area" />
      </div>

      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line
          v-if="ordersRecevied.analyticsData"
          :statistic="ordersRecevied.analyticsData.orders | k_formatter"
          :chart-data="ordersRecevied.series"
          icon="ShoppingBagIcon"
          statistic-title="Orders Received"
          color="warning"
          type="area" />
      </div>
    </div>

    <div class="vx-row">

      <!-- LINE CHART -->
      <div class="vx-col w-full md:w-2/3 mb-base">
        <vx-card title="Revenue">
          <template slot="actions">
            <feather-icon icon="SettingsIcon" svg-classes="w-6 h-6 text-grey"/>
          </template>
          <div slot="no-body" class="p-6 pb-0">
            <div v-if="revenueComparisonLine.analyticsData" class="flex">
              <div class="mr-6">
                <p class="mb-1 font-semibold">This Month</p>
                <p class="text-3xl text-success"><sup class="text-base mr-1">$</sup>{{ revenueComparisonLine.analyticsData.thisMonth.toLocaleString() }}</p>
              </div>
              <div>
                <p class="mb-1 font-semibold">Last Month</p>
                <p class="text-3xl"><sup class="text-base mr-1">$</sup>{{ revenueComparisonLine.analyticsData.lastMonth.toLocaleString() }}</p>
              </div>
            </div>
            <vue-apex-charts
              :options="analyticsData.revenueComparisonLine.chartOptions"
              :series="revenueComparisonLine.series"
              type="line"
              height="266" />
          </div>
        </vx-card>
      </div>

      <!-- RADIAL CHART -->
      <div class="vx-col w-full md:w-1/3 mb-base">
        <vx-card title="Goal Overview">
          <template slot="actions">
            <feather-icon icon="HelpCircleIcon" svg-classes="w-6 h-6 text-grey"/>
          </template>

          <!-- CHART -->
          <template slot="no-body">
            <div class="mt-10">
              <vue-apex-charts :options="analyticsData.goalOverviewRadialBar.chartOptions" :series="goalOverview.series" type="radialBar" height="240" />
            </div>
          </template>

          <!-- DATA -->
          <div slot="no-body-bottom" class="flex justify-between text-center mt-6">
            <div class="w-1/2 border border-solid d-theme-border-grey-light border-r-0 border-b-0 border-l-0">
              <p class="mt-4">Completed</p>
              <p class="mb-4 text-3xl font-semibold">786,617</p>
            </div>
            <div class="w-1/2 border border-solid d-theme-border-grey-light border-r-0 border-b-0">
              <p class="mt-4">In Progress</p>
              <p class="mb-4 text-3xl font-semibold">13,561</p>
            </div>
          </div>
        </vx-card>
      </div>
    </div>

    <div class="vx-row">

      <div class="vx-col w-full md:w-1/3 lg:w-1/3 xl:w-1/3 mb-base">
        <vx-card title="Sales Statistics">
          <div v-for="(browser, index) in browserStatistics" :key="browser.id" :class="{'mt-4': index}">
            <div class="flex justify-between">
              <div class="flex flex-col">
                <span class="mb-1">{{ browser.name }}</span>
                <h4>{{ browser.ratio }}%</h4>
              </div>
              <div class="flex flex-col text-right">
                <span class="flex -mr-1">
                  <span class="mr-1">{{ browser.comparedResult }}</span>
                  <feather-icon :icon=" browser.comparedResult < 0 ? 'ArrowDownIcon' : 'ArrowUpIcon'" :svg-classes="[browser.comparedResult < 0 ? 'text-danger' : 'text-success' ,'stroke-current h-4 w-4 mb-1 mr-1']"/>
                </span>
                <span class="text-grey">{{ browser.time | time(true) }}</span>
              </div>
            </div>
            <vs-progress :percent="browser.ratio"/>
          </div>
        </vx-card>
      </div>

      <div class="vx-col w-full md:w-2/3">
        <vx-card title="Client Retention">
          <div class="flex">
            <span class="flex items-center"><div class="h-3 w-3 rounded-full mr-1 bg-primary"/><span>New Clients</span></span>
            <span class="flex items-center ml-4"><div class="h-3 w-3 rounded-full mr-1 bg-danger"/><span>Retained Clients</span></span>
          </div>
          <vue-apex-charts :options="analyticsData.clientRetentionBar.chartOptions" :series="clientRetentionBar.series" type="bar" height="277" />
        </vx-card>
      </div>
    </div>
  </div>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import VueApexCharts from 'vue-apexcharts';
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue';
import analyticsData from '@/views/ui-elements/card/analyticsData.js';
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
            data: [10, 8, 12, 6, 9, 16],
          },
        ],
        analyticsData: {
          sales: '55%',
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
      siteTraffic: {
        series: [
          {
            name: 'Traffic Rate',
            data: [150, 200, 125, 225, 200, 250],
          },
        ],
      },
      activeUsers: {
        series: [
          {
            name: 'Active Users',
            data: [750, 1000, 900, 1250, 1000, 1200, 1100],
          },
        ],
      },
      newsletter: {
        series: [
          {
            name: 'Newsletter',
            data: [365, 390, 365, 400, 375, 400],
          },
        ],
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
      goalOverview: {
        analyticsData: {
          completed: 786617,
          inProgress: 13561,
        },
        series: [83],
      },
      sessionsData: {
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
      browserStatistics: [
        {
          id: 1,
          name: 'Rice',
          ratio: 73,
          time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
          comparedResult: '800',
        },
        {
          id: 3,
          name: 'Milo',
          ratio: 8,
          time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
          comparedResult: '-200',
        },
        {
          id: 2,
          name: 'Peak Milk',
          ratio: 19,
          time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
          comparedResult: '100',
        },
        {
          id: 4,
          name: 'Indomie',
          ratio: 27,
          time: 'Mon Dec 10 2018 07:46:05 GMT+0000 (GMT)',
          comparedResult: '-450',
        },
      ],
      clientRetentionBar: {
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
      chatLog: [],
      chatMsgInput: '',
      customersData: {},

      analyticsData,
      settings: { // perfectscrollbar settings
        maxScrollbarLength: 60,
        wheelSpeed: 0.60,
      },
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
