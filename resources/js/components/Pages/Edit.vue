<template>
  <div class="flex flex-col" v-if="!_.isEmpty(data)">
    <div class="flex items-center justify-between">
      <i
        @click="goToBack()"
        class="ri-arrow-left-line cursor-pointer text-lg w-fit"
      ></i>

      <div class="flex justify-end">
        <lava-button @click="update" :disabled="couldUpdate"
          >Update
        </lava-button>
      </div>
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

              <div v-for="(designField, i) in field.fields" :key="i">

                <div class="flex justify-start p-2 text-lg">

                  <div style="width: 18vw">{{ designField.name }} <span v-if="designField.rules.includes('required')" class="text-danger">*</span></div>

                  <component
                          class="w-full"
                          v-bind="designField.attributes"
                          v-if="designField.showOnForm"
                          :key="i"
                          :is="designField.component + '-edit'"
                          :data="designField"
                          :value="resourceValue(data, designField.column)"
                          @on-change="changed"/>

                </div>

                <form-error v-if="errors[field.column]" :errors="errors[field.column]"></form-error>

              </div>

            </template>
          </component>

          <div v-else class="flex justify-start p-2 text-lg">
            <div style="width: 18vw">{{ field.name }} <span v-if="field.rules.includes('required')" class="text-danger">*</span></div>

            <component
              class="w-full"
              v-bind="field.attributes"
              :key="index"
              v-if="field.showOnForm"
              :is="field.component + '-edit'"
              :data="field"
              :value="resourceValue(data, field.column)"
              @on-change="changed"
            />
          </div>
          <form-error v-if="errors[field.column]" :errors="errors[field.column]"></form-error>
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
      newData: {},
      resource: this.activeTool(),
      errors: [],
      canUpdate: false,
    };
  },
  computed: {
    couldUpdate() {
      if (!this.canUpdate) {
        return true;
      }

      return this.newData.length === 0;
    },
  },
  mounted() {
    this.errors = [];
    this.$http
      .post("/api/form", {
        resource: this.resource.resource,
        search: decodeURIComponent(this.$route.params.primaryKey),
        primary_key: this.resource.primaryKey,
      })
      .then((res) => {
        this.data = res.data;
      });
  },
  methods: {
    changed(value, column) {
      this.canUpdate = true;
      console.log(this.newData);
      this.$set(this.newData, column, value);
    },
    update() {
      this.$http
        .post("/api/update", {
          resource: this.resource.resource,
          data: this.newData,
          primary_key: this.resource.primaryKey,
          search: decodeURIComponent(this.$route.params.primaryKey),
        })
        .then((res) => {
          if (res) {
            Lava.toast(res.data.message, "success");
            this.canUpdate = false;
          }
        })
        .catch((error) => {
          this.errors = error.response.data.errors || [];
          this.canUpdate = false;
        });
    },
  },
};
</script>
