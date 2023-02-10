<template>
  <apexchart type="bar" height="100%" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts';

export default {
  name: 'ApexBarChart',
  components: {
    apexchart: VueApexCharts
  },
  props: {
    categories: [Array]
  },

  data() {
    return {
      series: [],
      chartOptions: {
        // 色濃い目
        colors: ['#59A5FF', '#D1E4FF'],
        // colors: ["#99E5EE", "#64B5F6"],
        // background: ["#99E5EE", "#64B5F6"],
        chart: {
          type: 'bar',
          zoom: {
            enabled: false
          }
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '26%',
            endingShape: 'rounded',
            borderRadius: 5
          }
        },
        dataLabels: {
          // 棒グラフ上の数値表示・非表示
          enabled: false,
          offsetX: -6,
          style: {
            fontSize: '12px',
            colors: ['#fff']
          }
        },
        stroke: {
          // 棒グラフとの間に隙間ができる
          show: true,
          width: 2,
          colors: ['transparent']
        },
        xaxis: {
          categories: this.categories ? this.categories : []
        },
        yaxis: {
          labels: {
            formatter: function (x) {
              x = x.toString();
              const pattern = /(-?\d+)(\d{3})/;
              while (pattern.test(x)) x = x.replace(pattern, '$1,$2');
              return x;
            }
          }
        },
        fill: {
          type: 'gradient',
          opacity: 1,

          gradient: {
            type: 'vertical',
            shadeIntensity: 0.1,
            inverseColors: false,
            stops: [0, 30, 100]
          }
        },
        tooltip: {
          background: ['#99E5EE', '#64B5F6'],
          shared: true,
          intersect: false
        }
      }
    };
  },
  watch: {
    categories(props) {
      this.chartOptions = {
        ...this.chartOptions,
        xaxis: {
          categories: props
        }
      };
    },
    series(props) {
      this.series = [];
      setTimeout(() => {
        this.series = props;
      }, 500);
    }
  },
  mounted() {
    // this.chartOptions = {
    //   ...this.chartOptions,
    //   xaxis: {
    //     categories: this.categories
    //   }
    // };
  }
};
</script>

<style lang="scss" scope>
/* 凡例が消えないので */
/* div.apexcharts-legend, div.apexcharts-toolbar {
  display: none;
} */
div.apexcharts-legend {
  display: none;
}
.vue-apexcharts {
  min-height: 60vh;
}
</style>
