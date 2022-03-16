<template>

    <div>

        <label class="cursor-pointer">

            {{ placeholder }}

            <input class="hidden"
                   ref="fileInput"
                   type="file"
                   :accept="accept"
                   :multiple="multiple"
                   @change="change">

        </label>


    </div>

</template>
<script>

    export default {
        props: ['placeholder', 'accept', 'multiple'],
        methods:{
            change($event){
                
                this.$nextTick(() => {
                    this.$emit('on-change', $event)
                    this.$refs.fileInput.value = null
                })

            }
        }
    }

</script>

