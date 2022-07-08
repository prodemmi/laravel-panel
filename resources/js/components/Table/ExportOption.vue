<template>

  <option-base>

    <template v-slot:title>
      <i class="ri-download-2-line"></i>
    </template>
    
    <template v-slot:body>

      <div class="flex-center">
        <lava-select style="width: 160px" :value="0" :options="options" @on-change="val => value = _.upperCase(options[val])" :nullable="false"></lava-select>

        <lava-button style="width: 80px" class="ltr:ml-1 rtl:mr-1 px-2" block @click="$emit('on-export', value)">Export</lava-button>

      </div>

    </template>

  </option-base>
  
</template>

<script>

import OptionBase from "./OptionBase";

export default {
  components: {
    OptionBase
  },
  data() {
    return {
      options: ['excel', 'json', 'print'],
      value: 'EXCEL'
    }
  }
};
</script>