<template>
  <vx-card>

    <vs-tabs>
      <vs-tab label="BY PRODUCTS">
        <div class="tab-text">
          <br>
          <view-by-product />
        </div>
      </vs-tab>
      <vs-tab label="BY STAFF">
        <div class="tab-text">
          <br>
          <view-by-staff />
        </div>
      </vs-tab>
    </vs-tabs>

  </vx-card>
</template>

<script>
import ViewByProduct from './ViewByProduct';
import ViewByStaff from './ViewByStaff';
export default {
  name: 'Customers',
  components: { ViewByProduct, ViewByStaff },
  data() {
    return {
    };
  },
  methods: {
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
