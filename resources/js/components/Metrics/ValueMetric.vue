<template>
    <div v-bind="metric.attributes" class="flex flex-col bg-gray-300 rounded shadow-lg p-2 mx-1 my-2">
        
        <div class="flex items-center justify-between">
            <b>{{metric.title}}</b>
            <lava-select style="min-width: 160px;width: fit-content" :nullable="false" :value="range" :options="this.metric.ranges" @on-change="value => getData(value)"></lava-select>
        </div>

        <div>
            <b style="font-size: 26px">{{metric.prefix}}{{value}}{{metric.suffix}} </b>
            <div class="flex justify-between my-1" :style="{color: percent >= 1 ? 'green' : 'red' }">
                <span>{{Math.abs(percent)}} % <span class="mx-1">{{getlabel}}</span></span>
                <i v-show="percent != 0" :class="`ri-arrow-right-${percent > 1 ? 'up' : 'down'}-line`"></i>
            </div>
        </div>

        <span v-if="metric.helpText" class="text-gray-700">{{metric.helpText}}</span>

    </div>
</template>

<script>

    export default {
        props: ['metric'],
        data() {
            return {
                value: null,
                percent: null,
                range: this.range || this.metric.range
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

                this.$http.post("/api/metric/get-metric-data", {name: this.metric.name, range: range || this.range}).then(res => {

                    this.value = res.data.value
                    this.percent = res.data.percent

                })

            }
        }
    }
</script>

