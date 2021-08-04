
<template>
  <div v-loading="loader">
    <div id="user-customerData">
      <vx-card>
        <div class="vx-row">
          <div class="vx-col lg:w-5/6 w-full">
            <div class="flex items-end px-3">
              <feather-icon svg-classes="w-6 h-6" icon="MapPinIcon" class="mr-2" />
              <span class="font-medium text-lg">Customers Map Location</span>
            </div>
            <vs-divider />
          </div>
          <div class="vx-col lg:w-1/6 w-full">
            <div class="flex items-end px-3">
              <span class="vx-col flex-1">
                <router-link
                  :to="'/customers/index'"
                >
                  <el-button
                    round
                    class="filter-item"
                    type="danger"
                    icon="el-icon-back"
                  >Go Back
                  </el-button>
                </router-link>
              </span>
            </div>
          </div>
        </div>

        <div class="carousel-example">
          <gmap-map
            :center="center"
            :zoom="zoom"
            style="width:100%;  height: 650px"
          >
            <gmap-cluster :zoom-on-click="true">
              <gmap-marker
                v-for="(m, index) in markers"
                :key="index"
                :position="m.position"
                :icon="icon"
                @click="center=m.position; showDetails(m.detail)"
              />
            </gmap-cluster>
          </gmap-map>
        </div>
      </vx-card>

    </div>
  </div>
</template>

<script>
import GmapCluster from 'vue2-google-maps/dist/components/cluster';
import Resource from '@/api/resource';
const allCustomersResource = new Resource('customers/all');
export default {
  components: {
    GmapCluster,
  },
  data() {
    return {
      // /////////////for map /////////////////
      center: { lat: 3.3792, lng: 6.5244 }, // default to greenlife office
      zoom: 7,
      icon: '/images/map-marker.png',
      // ////////////////////////////////////
      markers: [],
      loader: false,
      customers: [],
    };
  },
  mounted() {
    this.fetchAllCustomers();
    // this.addMarker();
  },
  methods: {
    fetchAllCustomers() {
      this.loader = true;
      allCustomersResource
        .list()
        .then((response) => {
          this.customers = response.customers;
          this.center = { lat: this.customers[0].latitude, lng: this.customers[0].longitude };
          this.addMarker();
          this.loader = false;
        });
    },
    addMarker() {
      var markers = [];
      const icon = '/images/map-marker.png';
      this.customers.forEach(customer => {
        const position = {
          lat: customer.latitude,
          lng: customer.longitude,
        };
        markers.push({ position: position, icon: icon, detail: customer });
      });
      this.markers = markers;
    },
    showDetails(customer){
      this.$vs.dialog({
        color: 'primary',
        title: customer.business_name,
        // eslint-disable-next-line quotes
        text: "Address: " + customer.address + "\nArea: " + customer.area,
        acceptText: 'Ok',
        // accept:this.acceptAlert
      });
    },
    randomColor() {
      const colorCodes = ['#fadcb6', '#c9fab6', '#c1fab6', '#b6faef', '#b6d6fa', '#bab6fa', '#e4b6fa', '#fab6b9'];
      const randomColor = colorCodes[Math.floor(Math.random() * colorCodes.length)];
      return this.hexToRgbA(randomColor);
    },
    hexToRgbA(hex){
      var c;
      if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
        c = hex.substring(1).split('');
        if (c.length === 3){
          c = [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c = '0x' + c.join('');
        return 'rgba(' + [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(',') + ',0.7)';
      }
      return 'rgba(250, 220, 182, 0.7)';
    },
  },
};

</script>

<style lang="scss">
#avatar-col {
  width: 10rem;
}

#page-user-view {
  table {
    td {
      vertical-align: top;
      min-width: 140px;
      padding-bottom: .8rem;
      word-break: break-all;
    }

    &:not(.permissions-table) {
      td {
        @media screen and (max-width:370px) {
          display: block;
        }
      }
    }
  }
}

// #account-info-col-1 {
//   // flex-grow: 1;
//   width: 30rem !important;
//   @media screen and (min-width:1200px) {
//     & {
//       flex-grow: unset !important;
//     }
//   }
// }

@media screen and (min-width:1201px) and (max-width:1211px),
only screen and (min-width:636px) and (max-width:991px) {
  #account-info-col-1 {
    width: calc(100% - 12rem) !important;
  }

  // #account-manage-buttons {
  //   width: 12rem !important;
  //   flex-direction: column;

  //   > button {
  //     margin-right: 0 !important;
  //     margin-bottom: 1rem;
  //   }
  // }

}

</style>
