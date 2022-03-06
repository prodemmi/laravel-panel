<template>

    <div class="flex justify-between items-center">
        <div class="relative w-full ltr:mr-4 rtl:ml-4">
            <input class="text-input w-full" 
            :type="show || env == 'create' ? 'text' : 'password'" 
            v-bind="data.attributes" 
            v-model="model" @input="onChange"
            :disabled="!canEdit"/>
            <i v-if="canEdit && _.size(model) > 0" :class="['absolute ltr:right-0 rtl:left-0 top-0.5', show ? 'ri-eye-line' : 'ri-eye-off-line' ]" @click="show = !show"></i>
            <i v-else-if="!canEdit" class="absolute ltr:right-0 rtl:left-0 top-0.5 cursor-pointer ri-edit-line" @click="edit()"></i>
        </div>
        <lava-button v-if="canEdit" @click="generate" small>Generate</lava-button>
    </div>

</template>

<script>

    import {FormMixin} from '../../../mixins'

    export default {
        props: ['data', 'value', 'env'],
        mixins: [FormMixin],
        data(){
            return {
                show: false,
                canEdit: this.env == 'create' ? true : false
            }
        },
        methods:{
            generate(){
                this.onChange(this.model = Math.random().toString(36).slice(2))
            },
            edit(){
                this.canEdit = true
                this.show = true
                this.model = null
            }
        }
    }
</script>