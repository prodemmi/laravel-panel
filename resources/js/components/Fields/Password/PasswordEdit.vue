<template>

    <div class="flex justify-between items-center">
        <div class="relative w-full mr-4">
            <input class="text-input w-full" 
            :type="show || env == 'create' ? 'text' : 'password'" 
            v-bind="data.attributes" 
            v-model="model" @input="onChange"
            :disabled="!canEdit"/>
            <i v-if="canEdit && _.size(model) > 0" :class="['absolute right-0 top-1', show ? 'ri-eye-line' : 'ri-eye-off-line' ]" @click="show = !show"></i>
            <i v-else-if="!canEdit" class="absolute right-0 top-1 cursor-pointer ri-edit-line" @click="canEdit = true;show = true"></i>
        </div>
        <lava-button v-if="canEdit" @click="generate" small>Generate</lava-button>
    </div>

</template>

<script>

    import {FormMixin} from '../../../mixins'

    export default {
        name: "password-edit",
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
            }
        }
    }
</script>