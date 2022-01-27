<template>

  <ckeditor
      v-bind="data.attributes"  
      ref="editor"
      v-if="env == 'create' || model"
      v-model="model"
      :editor="editor"
      @input="onChange"/>
    
</template>

<script>
import { FormMixin } from "../../../mixins";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

export default {
  name: "ckeditor-edit",
  props: ["data", "value", 'env'],
  mixins: [FormMixin],
  data() {
    return {
      editor: ClassicEditor
    };
  }
};
</script>
