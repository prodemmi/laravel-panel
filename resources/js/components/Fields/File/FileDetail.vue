<template>
  <div class="flex flex-col">
    <div class="flex items-center flex-wrap">
      <template v-for="(url, i) in getShowingValue">
        <div class="relative mb-2 ltr:mr-2 rtl:ml-2" :key="i">
          <lava-tooltip :text="url">
            <div
              v-if="url"
              class="flex items-center justify-center text-3xl cursor-pointer"
              :class="{
                'h-24 w-24 rounded border-solid border-2 border-slate-300':
                  data.fileType === 'image',
              }"
              @click="data.fileType === 'image' ? previewImage(url) : copyToClipboard(url)"
              v-bind="data.attributes"
            >
              <img
                v-if="data.fileType === 'image'"
                class="shadow-lg object-cover w-full h-full"
                v-lazy="url"
                alt=""
              />
              <audio v-else-if="data.fileType === 'audio'" controls>
                <source :src="url" :type="data.acceptTypes" />
              </audio>
              <video v-else-if="data.fileType === 'video'" controls>
                <source :src="url" :type="data.acceptTypes" />
              </video>
              <div v-else v-bind="data.attributes">
                {{ getThumbName(url) }}
              </div>
            </div>
          </lava-tooltip>

          <span
            v-if="deletable"
            @click="$emit('on-delete', url)"
            style="width: 28px; height: 28px"
            class="
              ri-close-line
              absolute
              z-60
              -top-2
              ltr:-right-1
              rtl:-left-1
              cursor-pointer
              rounded-full
              bg-primary
              text-white
              flex
              items-center
              justify-center
            "
          ></span>
        </div>
      </template>
    </div>

    <div
      v-if="data.multiple && !(getShowingValue.length >= getValue.length)"
      class="flex items-center justify-between cursor-pointer w-full"
    >
      <div class="flex justify-between w-full">
        <div class="flex items-center">
          <span class="ltr:mr-2 rtl:ml-2">Show</span>
          <span class="ltr:mr-1 rtl:ml-1 text-blue-400" @click="showMore"
            >more</span
          >
          <span class="mx-1">/</span>
          <span @click="showAll" class="text-blue-400">all</span>
          <div class="ltr:ml-2 rtl:mr-2">
            <lava-spinner v-if="loading" color="primary"></lava-spinner>
          </div>
        </div>
        <div v-show="data.multiple && getValue && getValue.length">
          {{ getShowingValue.length }} / {{ getValue.length }}
        </div>
      </div>
      <div
        v-if="removable && getValue.length > 0"
        @click="$emit('remove-all')"
        class="text-danger"
      >
        Remove all
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["data", "value", "deletable", "removable"],
  data() {
    return {
      page: 1,
      loading: false,
    };
  },
  computed: {
    getValue() {
      if (this.value === null || this.value === undefined) {
        return [];
      }

      if (_.isArray(this.value) && _.isEmpty(this.value)) {
        return [];
      }

      if (_.isArray(this.value)) {
        return this.value;
      }

      return [this.value];
    },
    getShowingValue() {
      return this.getValue.slice(0, 6 * this.page);
    },
  },
  methods: {
    showMore() {
      this.loading = true;
      setTimeout(() => {
        this.page++;
        this.loading = false;
      }, 400);
    },
    showAll() {
      this.loading = true;
      setTimeout(() => {
        this.page = Number.MAX_SAFE_INTEGER;
        this.loading = false;
      }, 400);
    },
    getThumbName(url) {
      if (!url) {
        return null;
      }

      return _.upperCase(url.split(".").pop());
    },
    
  },
};
</script>