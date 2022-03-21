<template>

    <div class="flex flex-col">
        
        <div v-if="model.length < data.maxFiles" class="flex flex-col mb-5">

            <lava-button v-if="max > 0 && !loading" small rounded :no-padding="true" style="max-width: 120px">
                <FileInput placeholder="Add file"
                            @on-change="change"
                            :accept="data.acceptTypes"
                            :multiple="data.multiple"/>
            </lava-button>

            <div>

                <div v-if="data.maxFiles > 1 && data.maxFiles <= Number.MAX_SAFE_INTEGER">

                    Limit: {{data.maxFiles - model.length}}

                </div>

            </div>

        </div>

        <FileDetail v-if="!_.isEmpty(model)" class="ltr:mr-2 rtl:ml-2" :data="data" :value="model" :removable="true" :deletable="true" @remove-all="removeAll" @on-delete="i => removeFile(i)"/>

    </div>

</template>

<script>

    import FileDetail from './FileDetail'
    import FileInput from '../../FileInput'

    export default {
        data() {
            return {
                model: this.value === null || this.value === undefined ? [] : _.isArray(this.value) ? this.value: [this.value],
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
                        value: this.data?.multiple ? this.model : _.first(this.model), 
                        column: this.data.column,
                        file: true,
                        multiple: this.data?.multiple || false
                    })

                }).catch((error) => {

                    this.model = []
                    this.loading = false

                })

            },
            removeFile(url) {
                
                this.$delete(this.model, _.findIndex(this.model, (m) => m === url))
                Lava.showLoading(false)
                this.$emit('on-change', {
                    value: _.isEmpty(this.model) ? null : this.model, 
                    column: this.data.column,
                    all: this.value === null || this.value === undefined ? [] : _.isArray(this.value) ? this.value: [this.value],
                    file: true,
                    multiple: this.data.multiple,
                    delete: true
                })

            },
            removeAll() {
                
                this.$nextTick(() => {

                    this.model = []
                    Lava.showLoading(false)
                    this.$emit('on-change', {
                        value: null, 
                        column: this.data.column,
                        file: true,
                        deleteAll: true
                    })

                })

            }
        }
    }
</script>