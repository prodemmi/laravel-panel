<template>
  <div>
    <input
      type="checkbox"
      class="checkbox"
      v-model="model"
      value="true"
      @change="onChange"
      :true-value="true"
      :false-value="false"
    />
  </div>
</template>

<script>
import { FormMixin } from "../../../mixins";
export default {
  name: "boolean-detail",
  props: ["data", "value"],
  mixins: [FormMixin],
};
</script>
