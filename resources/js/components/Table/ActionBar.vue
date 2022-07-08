<template>
    <div class="flex items-center">

      <lava-select ref="select"
                   style="width: 220px"
                   class="ltr:mr-1 rtl:ml-1"
                   :placeholder="getLabel"
                   :value="selected_action"
                   @on-clear="selected_action = null"
                   @on-change="(value) => selected_action = getOptions[value]"
                   :style="{ direction: $store.getters.getConfig.rtl ? 'rtl': 'ltr'}"
                   :options="getOptions" />

      <lava-button
        @click="handleButton"
        :disabled="!selected_action"
        :color="getAction && getAction.danger ? 'danger' : 'primary'">Go</lava-button>

      <lava-button
        v-if="showClose"
        @click="$emit('on-close')"
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
      type: [Object, Array],
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
    getLabel(){
        return "Select an action" + ( _.isEmpty(this.selected) ? ' for all' : ( this.selected.length > 1 ? ' (' + this.selected.length + ' items ) ' : ''))
    },
    getAction(){
        return _.find(this.actions, { name: this.selected_action?.label })
    },
    getOptions(){

      return _.filter(this.actions, { onlyOnTable: false }).map((action, index) => ({
              value: index,
              label: action.name,
              danger: action.danger
            }))

    }
  },
  methods: {
    handleButton(){
      this.$emit('handle-action', this.getAction, this.selected )
      this.$refs.select.clear()
    }
  }
};
</script>
