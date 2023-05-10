<template>
    <div class="d-flex justify-content-center">
        <div v-if="emptyResult" class="mb-5">
            <span class="text-2xl text-bold text-center">No results to show</span>
        </div>
        <apexchart
            v-else
            ref="chart"
            width="1000"
            type="line"
            :options="chartOptions"
            :series="series"
        ></apexchart>
    </div>
</template>

<script>
import {get, set} from "lodash";

const TYPES = [
    'rpc',
    'php',
    'socket',
    'rabbitmq',
    'ffi',
]
export default {
    name: "Chart",
    data() {
        return {
            chartOptions: {
                chart: {
                    id: "vuechart-example",
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return value.toFixed(2) + ' ms';
                        }
                    },
                    min: 0,
                    max: 0,
                    tickAmount: 20,

                },
            },
            series: [],
            max: 0,
            apexChartComponent: null,
            emptyResult: false
        };
    },
    async mounted() {
        await this.requestChartData()
        await this.$nextTick(() => {
            window.dispatchEvent(new Event('resize'));
        });
    },
    methods: {
      createDataByType(resultByType = []) {
          let data = []
          let index = 0
          resultByType.forEach(result => {
              if(result.execution_time > this.max) {
                  this.max = result.execution_time
              }
              data.push([index++, result.execution_time])
          })

          return data
      },
    async requestChartData() {
        let response = await axios.post(
            "api/getChartData",
        )
        this.series = []

        const results = get(response, 'data')

        TYPES.forEach(type => {
            const data = this.createDataByType(results[type])
            this.series.push({
                name: type,
                data: data
            })
        })

        this.$refs.chart.updateOptions({
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return value.toFixed(2) + ' ms';
                    }
                },
                min: 0,
                max: this.max,
                tickAmount: 20,
            }
        })
    }
    }
}
</script>

<style scoped>

</style>
