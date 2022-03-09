<template>

    <div v-if="view">

        <div v-html="view" id="tool-asfasf" class="hidden"></div>
        <lava-button v-if="window.debug" @click="load" :loading="reloading">Refresh</lava-button>

        <div v-if="!reloading">

            <div v-html="template"></div>

        </div>

    </div>

</template>

<script>
export default {
    data(){
        return {
            template: null,
            script: null,
            reloading: false
        }
    },
    computed: {
        view() {
            return this.activeTool()?.view
        }
    },
    mounted(){

        this.load()

    },
    methods: {
        load() {

            this.reloading = true

            this.updateConfig(() => {

                this.$nextTick(() => {

                    this.reloading = false

                    var template = $('#tool-asfasf > template')
                    var script = $('#tool-asfasf > script')

                    if(template.length && script.length){

                        this.template = _.trim(template.html())
                        this.script = _.trim(script.html())

                    }else{

                        this.template = _.trim(template = $('#tool-asfasf').html())
                        return
                        
                    }

                })

                setTimeout(() => {

                    eval(this.script)
                    this.reloading = false

                }, 200);

            }).catch(() => {
                this.reloading = false
            })

        }
    }
}
</script>