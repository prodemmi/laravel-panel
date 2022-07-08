<template>
    <div v-bind="metric.attributes" class="flex flex-col bg-gray-300 rounded shadow-lg p-2 mx-1 my-2">
        
        <div class="flex items-center justify-between">
            <b>{{metric.title}}</b>
            <lava-select style="min-width: 160px;width: fit-content" :nullable="false" :value="metric.defaultRange" :options="metric.ranges" @on-change="value => getData(value)"></lava-select>
        </div>

        <div class="flex items-center justify-center w-full h-full">
            <lava-spinner v-if="loading" color="primary"></lava-spinner>
            <div class="w-full h-full" v-else>
                <b style="font-size: 26px">{{metric.prefix}}<span v-text="value"></span>{{metric.suffix}} </b>
                <div class="flex justify-between my-1" :style="{color: percent >= 1 ? 'green' : 'red' }">
                    <span>{{Math.abs(percent)}} % <span class="mx-1">{{getlabel}}</span></span>
                    <i v-show="percent != 0" :class="`ri-arrow-right-${percent > 1 ? 'up' : 'down'}-line`"></i>
            </div>
            </div>
        </div>

        <div v-if="metric.helpText" class="text-gray-700">{{metric.helpText}}</div>

    </div>
</template>

<script>

    export default {
        props: ['metric'],
        data() {
            return {
                value: null,
                loading: false,
                percent: null
            }
        },
        mounted() {

            this.getData()
            
        },
        computed: {
            getlabel() {
                
                switch (Math.sign(this.percent)) {
                    case 1:
                    return 'Increase'
                    case 0:
                    return 'Constant'
                    case -1:
                    return 'Decrease'
                }
            }
        },
        methods: {
            getData(range = null){

                this.loading = true

                this.$http.post("/api/metric/get-metric-data", {name: this.metric.name, range: range || this.metric.defaultRange}).then(res => {

                    this.loading = false
                    this.value = res.data.value
                    this.percent = res.data.percent

                })

            }
        }
    }
</script>

