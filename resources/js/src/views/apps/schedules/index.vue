<template>
  <full-calendar :events="fcEvents" locale="en" />

</template>

<script>
import FullCalendar from 'vue-fullcalendar';
export default {
  components: {
    FullCalendar, // make the <FullCalendar> tag available
  },
  data: function() {
    return {
      fcEvents: [{
        title: 'Sunny Out of Office',
        start: '2021-07-03',
        // end: '2017-07-27',
      }, {
        title: 'Sunny Out of Office',
        start: '2021-07-03',
        // end: '2017-07-27',
      }, {
        title: 'Sunny Out of Office',
        start: '2021-07-03',
        // end: '2017-07-27',
      }, {
        title: 'Sunny Out of Office',
        start: '2021-07-03',
        // end: '2017-07-27',
      }],
    };
  },
};
</script>
