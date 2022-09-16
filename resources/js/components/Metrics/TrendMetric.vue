<template>
    <div v-bind="metric.attributes" class="flex flex-col bg-gray-300 rounded shadow-lg p-2 mx-1 my-2">
        
        <div class="flex items-center justify-between">
            <b>{{metric.title}}</b>
            <lava-select style="min-width: 160px;width: fit-content" :searchable="false" :nullable="false" :value="metric.defaultRange" :options="metric.ranges" @on-change="value => getData(value)"></lava-select>
        </div>

        <span v-if="data.data && data.data.length">Today: {{_.last(data.data).value}}</span>

        <div class="flex items-center justify-center w-full" style="height: 120px">
            <lava-spinner v-if="loading" color="primary"></lava-spinner>
            <div v-else ref="chart" class="w-full h-full" style="margin-left: -16px"></div>
        </div>

        <div v-if="metric.helpText" class="text-gray-700">{{metric.helpText}}</div>

    </div>
</template>

<script>

    import ApexCharts from 'apexcharts'

    export default {
        props: ['metric'],
        data() {
            return {
                data: {},
                loading: false               
            }
        },
        mounted() {

            this.getData()
            
        },
        methods: {
            createChart(){

                var options = {
                    stroke: {
                        width: 1.4
                    },
                    colors: [this.getThemeColor('--primary')],
                    chart: {
                        type: 'line',
                        height: '100%',
                        animations: {
                            easing: 'easeout'
                        },
                        toolbar: {
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                                reset: false,
                                customIcons: []
                            },
                        },
                    },
                    tooltip: {
                        x: {
                            show: false
                        }
                    },
                    markers: {
                        size: 4,
                    },
                    grid: {
                        show: false
                    },
                    xaxis: {
                        labels:{
                            show: false
                        }
                    },
                    yaxis: {
                        labels:{
                            show: false
                        }
                    },
                    labels: _.map(this.data?.data || [], 'label'),
                    noData: {
                        text: 'No data'
                    },
                    series: [{
                        name: this.data.name,
                        data: _.map(this.data?.data || [], data => ({
                            x: this.data.period + ' ' + data.label,
                            y: data.value
                        }))
                    }]
                }

                if(this.metric.disableLabels){
                    // options.xaxis.labels.show = true
                    // options.yaxis.labels.show = true
                }

                var chart = new ApexCharts(this.$refs.chart, options);

                chart.render();

            },
            getData(range = null){

                this.loading = true

                return this.$http.post("/api/metric/get-metric-data", {name: this.metric.name, range: range || this.metric.defaultRange}).then(res => {

                        this.loading = false
                        this.data = res.data

                        this.$nextTick(() => {

                            this.createChart()

                        })

                })

            }
        }
    }
</script>

