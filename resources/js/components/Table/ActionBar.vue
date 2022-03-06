<template>
    <div class="flex items-center">
      
      <VueSelect style="width: 220px;height: 40px;"
                 :style="{ direction: $store.getters.getConfig.rtl ? 'rtl': 'ltr'}"
                 class="ltr:mr-1 rtl:ml-1"
                 placeholder="Select an action"
                 v-model="selected_action"
                 :options="getOptions"
                 :reduce="(option) => option">
      </VueSelect>
      
      <lava-button
        @click="$emit('handle-action', getAction, selected)"
        :disabled="!selected_action"
        :color="getAction && getAction.danger ? 'danger' : 'primary'"
        no-padding
        >Go
      </lava-button>

      <lava-button
        v-if="showClose"
        @click="$emit('on-close')"
        no-padding
        ><i class="ri-close-line cursor-pointer" ></i>
      </lava-button>
      
    </div>
</template>

<script>
import VueSelect from "vue-select";
export default {
  components: {
    VueSelect,
  },
  props: {
    actions :{
      type: [Object, Array],
      required: false,
      default: () => [],
    },
    selected :{
      type: Array,
      required: false,
      default: () => [],
    },
    showClose: {
      type: Boolean,
      default: true,
    }
  },
  data() {
    return {
      selected_action: null,
    };
  },
  computed: {
    getAction(){
        return _.find(this.actions, { name: _.trimEnd(this.selected_action?.label, ' all') })
    },
    getOptions(){
      
      return _.filter(this.actions, { onlyOnTable: false }).map((action, index) => ({
                value: index,
                label: action.name + (_.isEmpty(this.selected) ? ' all' : '')
              }))

    }
  }
};
</script>