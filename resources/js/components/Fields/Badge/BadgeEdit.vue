<template>

    <select v-bind="data.attributes" v-model="model" class="select" @change="onChange">
        <option v-for="(title, value) in data.options" :key="value" :value="value" v-text="title"></option>
    </select>

</template>

<script>
import { FormMixin } from "../../../mixins";
export default {
  name: "badge-edit",
  props: ["data", "value"],
  mixins: [FormMixin]
};
</script>