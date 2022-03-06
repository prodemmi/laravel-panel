<template>
  <div
    v-if="!data.asHtml"
    v-bind="data.attributes"
    class="rounded-full w-2 h-2"
    :class="value ? 'bg-green-600' : 'bg-red-600'"
  ></div>
  <div v-else v-bind.prop="getValue"></div>
</template>

<script>
import { asHtmlMixin } from "../../../mixins";

export default {
  props: ["data", "value"],
  mixins: [asHtmlMixin],
};
</script>