<template>
  <div class="flex flex-col" v-if="!_.isEmpty(data)">
    <div class="flex items-center justify-between">
      <i
        @click="goToBack()"
        class="ri-arrow-left-line cursor-pointer text-lg w-fit"
      ></i>

      <lava-button
        @click="
          goToRoute('edit', {
            id: $route.params.id,
            resource: $route.params.resource,
          })
        "
      >
        Edit
      </lava-button>
    </div>

    <div class="p-2 text-lg bg-white shadow rounded-md my-2">
      <template v-for="(field, index) in resource.fields">
        <div v-if="field.showOnForm" :key="index">

          <component
            v-if="field.forDesign"
            v-bind="field.attributes"
            :data="field"
            :is="field.component"
          >
            <template v-slot:header>{{ field.title }}</template>

            <template v-slot:body>
              <div
                v-for="(designField, i) in field.fields"
                :key="i"
                class="flex justify-start p-2 text-lg"
              >
                <div style="width: 18vw">{{ designField.name }}</div>

                <component
                  v-bind="designField.attributes"
                  v-if="designField.showOnDetail"
                  :key="i"
                  :is="designField.component + '-detail'"
                  :data="designField"
                  :value="resourceValue(data, designField.column)"
                />
              </div>
            </template>
          </component>

          <div v-else class="flex justify-start p-2 text-lg">
            <div style="width: 18vw">{{ field.name }}</div>

            <component
              v-bind="field.attributes"
              :key="index"
              v-if="field.showOnDetail"
              :is="field.component + '-detail'"
              :data="field"
              :value="resourceValue(data, field.column)"
            />
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      data: [],
      resource: this.activeTool(),
    };
  },
  mounted() {
    this.$http
      .post("/api/detail", {
        resource: this.resource.resource,
        search: decodeURIComponent(this.$route.params.primaryKey),
        primary_key: this.resource.primaryKey,
      })
      .then((res) => {
        this.data = res.data;
      });
  },
};
</script>
