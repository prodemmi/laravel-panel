<template>

  <div class="card">

    <h3 class="card--header" v-if="!!$slots.header">

      <slot name="header"></slot>

    </h3>

    <div class="card--body">

      <slot name="body"></slot>

      <div class="card--footer" v-if="!!$slots.footer">

        <slot name="footer"></slot>

      </div>

    </div>

  </div>

</template>