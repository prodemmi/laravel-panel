<template>

    <div class="flex flex-col">
        
        <div v-if="model.length < data.maxFiles" class="flex items-end mb-5">

            <div class="mx-1">
                <div v-if="data.acceptTypes">

                    Accepts: {{data.acceptTypes}}

                </div>

                <div v-if="data.maxFiles > 1">

                    Limit: {{data.maxFiles - model.length}}

                </div>
            </div>

            <lava-button v-if="max > 0 && !loading" small rounded :no-padding="true">
                <FileInput placeholder="Add file"
                            @on-change="change"
                            :accept="data.acceptTypes"
                            :multiple="data.multiple"/>
            </lava-button>

        </div>

        <FileDetail class="ltr:mr-2 rtl:ml-2" :data="data" :value="_.isArray(model) ? model: [model]" :removable="true" :deletable="true" @remove-all="removeAll" @on-delete="i => removeFile(i)"/>

    </div>

</template>

<script>

    import FileDetail from './FileDetail'
    import FileInput from '../../FileInput'

    export default {
        data() {
            return {
                model: _.isArray(this.value) ? this.value: [this.value],
                loading: false
            }
        },
        components: {
            FileDetail,
            FileInput
        },
        props: ['data', 'value'],
        computed: {
            max() {
                return Math.abs(this.data.maxFiles - this.model.length)
            }
        },
        methods: {
            change(e) {

                this.loading = true

                var files = _.takeRight(e.target.files, this.max);

                const formData = new FormData();
                _.each(files, (file, i) => formData.append(`file-${i}`, files[i]))

                formData.append('column', this.data.column)
                formData.append('disk', this.data?.disk)
                formData.append('resource', this.activeTool()?.resource)

                this.$http.post('api/media/upload', formData, {
                        onUploadProgress: progressEvent => Lava.showLoading(progressEvent.loaded),
                        'Content-Type': 'multipart/form-data'
                    }
                ).then((res) => {

                    this.loading = false
                    this.model = [...this.model, ..._.map(res.data, 'url')]
                    Lava.showLoading(false)

                    this.$emit('on-change', {
                        value: this.model, 
                        column: this.data.column,
                        file: true
                    })

                }).catch((error) => {

                    this.model = []
                    this.loading = false

                })

            },
            removeFile(i) {

                this.$delete(this.model, i)
                Lava.showLoading(false)
                this.$emit('on-change', {
                    value: this.model, 
                    column: this.data.column,
                    file: true
                })

            },
            removeAll() {
                
                this.$nextTick(() => {

                    this.model = []
                    Lava.showLoading(false)
                    this.$emit('on-change', {
                        value: this.model, 
                        column: this.data.column,
                        file: true
                    })

                })

            }
        }
    }
</script>