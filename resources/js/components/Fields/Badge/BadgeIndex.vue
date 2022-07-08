<template>
  <div
    v-bind="data.attributes"
    class="badge"
    :class="data.types[data.data[value]] || 'info'"
    v-bind.prop="getValue"
  ></div>
</template>

<script>
import { asHtmlMixin } from "../../../mixins";

export default {
  props: ["data", "value"],
  mixins: [asHtmlMixin]
};
</script>