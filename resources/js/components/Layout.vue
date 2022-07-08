<template>
  <div :id="id">
    <slot name="body"></slot>
  </div>
</template>

<script>
export default {
  props: ["data"],
  data() {
    return {
      id: this.ukey(),
    };
  },
  mounted() {
    var layout = $(`#${this.id} > div`);
    layout.addClass("flex");
    layout.addClass("w-full");

    if (this.data?.direction === "column") layout.addClass("flex-col");
  },
};
</script>