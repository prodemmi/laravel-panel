<template>

    <ckeditor v-bind="data.attributes"
              class="w-full"
              v-if="env === 'create' || model"
              v-model="model"
              :editor="editor"
              :config="editorConfig"
              @input="onChange"/>

</template>

<script>
    import {FormMixin} from "../../../mixins";
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

    export default {
        name: "ckeditor-edit",
        props: ["data", "value", 'env'],
        mixins: [FormMixin],
        mounted() {


        },
        data() {
            return {
                editor: ClassicEditor,
                editorConfig: {
                    ckfinder: {
                        uploadUrl: '/' + this.activePanel() + '/api/media/upload',
                    },
                },
            };
        }
    };
</script>
