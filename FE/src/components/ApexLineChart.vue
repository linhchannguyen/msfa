<template>
  <apexchart type="area" height="100%" :options="chart.options" :series="chart.series"></apexchart>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts';
export default {
  name: 'ApexLineChart',
  components: {
    apexchart: VueApexCharts
  },
  props: {
    categories: [Array],
    series: [Array],
    options: [Object]
  },
  data() {
    return {
      chart: {
        toolbar: {
          show: false
        },
        options: {
          chart: {
            zoom: {
              enabled: false
            }
          },
          dataLabels: {
            // 棒グラフ上の数値表示・非表示
            enabled: false
          },
          // 前年度 - 本年度
          colors: ['#fbcffc', '#d89cf6'],
          // colors: ["#E1BEE7", "#FFCDD2"],
          fill: {
            // グラフのグラデーション部分の色を設定
            colors: ['#ffe2ff', '#E1BEE7'],
            // colors: ["#FFCDD2", "#E1BEE7"],
            type: 'gradient',
            gradient: {
              type: 'vertical',
              shadeIntensity: 1,
              inverseColors: false,
              opacityFrom: 0.5,
              opacityTo: 0,
              stops: [0, 90, 100]
            },
            // 表示アニメーション（効いてない）
            animations: {
              enabled: true,
              easing: 'easeinout',
              speed: 800,
              animateGradually: {
                enabled: true,
                delay: 500
              },
              dynamicAnimation: {
                enabled: false,
                speed: 800
              }
            }
          },
          xaxis: {
            categories: []
            // labels: {
            //   formatter: function (x) {
            //     console.log(x);
            //     // x = x?.slice(5);
            //     return x;
            //   }
            // }
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
          }
        },
        series: [
          {
            // type: "line",
            data: []
          },
          {
            // type: "area",
            data: []
          }
        ]
      }
    };
  },
  watch: {
    categories(e) {
      let x = e.map((e1) => e1.slice(5));
      this.chart.options = {
        ...this.chart.options,
        xaxis: {
          categories: e,
          overwriteCategories: x,
          labels: {
            rotate: 0
          }
        }
      };
    },

    series(e) {
      this.chart.series = e;
    },
    options(e) {
      this.chart.options = e;
    }
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
