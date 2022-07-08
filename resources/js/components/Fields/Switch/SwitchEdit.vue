<template>
  <div class="switch-input" :class="{ 'bg-green-400' : model }">
      <div @click="onToggle" class="switch-input__container" :class="{ 'translate-x-4': model}"></div>
  </div>
</template>

<script>
import { FormMixin } from "../../../mixins";
export default {
  props: ["data", "value"],
  mixins: [FormMixin],
  methods: {
    onToggle(){

      this.model = !this.model
      this.onChange(this.model)

    }
  },
};
</script>
