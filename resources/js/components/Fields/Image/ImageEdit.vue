<template>

    <div class="flex items-center">

        <div class="flex flex-wrap mr-2">

            <transition-group name="fade">

                <div v-for="(image, i) in thumbnails" class="relative" :key="image">

                    <ImageDetail class="m-1" :data="data" :value="image"/>
                    <span v-html="icon('close')"
                          @click="removeImage(i)"
                          class="absolute -top-2 right-0 cursor-pointer text-lg"></span>

                </div>

            </transition-group>

        </div>

        <div>

            <div v-if="model.length <= data.maxFiles">

                <div class="mx-1">
                    <div v-if="data.acceptTypes">

                        Accepts: {{data.acceptTypes}}

                    </div>

                    <div v-if="data.maxFiles > 1">

                        Max: {{data.maxFiles}}

                    </div>
                </div>

                <lava-button v-if="max > 0" small rounded :no-padding="true">
                    <FileInput placeholder="Add file"
                               @on-change="change"
                               :accept="data.acceptTypes"
                               :multiple="data.multiple"/>
                </lava-button>

            </div>

        </div>

    </div>

</template>

<script>

    import ImageDetail from './ImageDetail'
    import FileInput from '../../FileInput'

    export default {
        name: "image-edit",
        data() {
            return {
                model: [this.value],
                thumbnails: [this.value],
                currentFiles: [this.value],
                loading: false
            }
        },
        components: {
            ImageDetail,
            FileInput
        },
        props: ['data', 'value'],
        computed: {
            max() {
                return Math.abs(this.data.maxFiles - this.currentFiles.length)
            }
        },
        methods: {
            change(e) {

                this.loading = true

                var files = _.takeRight(e.target.files, this.max);

                this.model = files

                const formData = new FormData();
                _.each(files, (file, i) => formData.append(`file-${i}`, files[i]))

                formData.append('column', this.data.column)
                formData.append('disk', this.data.disk)
                formData.append('resource', this.activeTool()?.resource)

                this.$http.post('api/media/upload', formData, {
                        onUploadProgress: progressEvent => Lava.showLoading(progressEvent.loaded),
                        'Content-Type': 'multipart/form-data'
                    }
                ).then((res) => {

                    this.loading = false
                    Lava.showLoading(false)

                    this.thumbnails = _.map(res.data, 'url')
                    this.currentFiles = [...this.currentFiles, ...this.thumbnails]
                    this.thumbnails = this.currentFiles

                    this.$emit('on-change', this.currentFiles, this.data.column)

                }).catch((error) => {

                    this.model = []
                    this.thumbnails = []
                    this.loading = false
                    Lava.showLoading(false)

                })

            },
            removeImage(i) {
                this.$delete(this.model, i)
                this.$delete(this.thumbnails, i)
                this.$delete(this.currentFiles, i)
                Lava.showLoading(false)
                this.$emit('on-change', this.currentFiles, this.data.column)

            }
        }
    }
</script>