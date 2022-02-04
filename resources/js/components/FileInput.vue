<template>

    <div>

        <label class="cursor-pointer">

            {{ placeholder }}

            <input class="hidden"
                   type="file"
                   :accept="accept"
                   :multiple="multiple"
                   @change="$emit('on-change', $event)">

        </label>


    </div>

</template>
<script>

    export default {
        props: ['placeholder', 'accept', 'multiple']
    }

</script>

