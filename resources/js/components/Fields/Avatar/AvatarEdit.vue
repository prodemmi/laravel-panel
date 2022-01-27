<template>

    <div class="flex items-center">

        <div v-if="model && !loading" class="mr-2">

            <AvatarDetail :data="data" :value="model"/>

        </div>

        <div>

            <lava-button v-if="model" @click="removeAvatar" small rounded>Remove</lava-button>

            <lava-button small rounded :no-padding="true" v-else>
                <FileInput placeholder="Choose avatar" @on-change="change"/>
            </lava-button>

        </div>

    </div>

</template>

<script>

    import AvatarDetail from './AvatarDetail'
    import FileInput from '../../FileInput'

    export default {
        name: "avatar-edit",
        data() {
            return {
                model: this.value,
                loading: false
            }
        },
        components: {
            AvatarDetail,
            FileInput
        },
        props: ['data', 'value'],
        methods: {
            change(e) {

                this.loading = true

                var file = e.target.files[0];
                var reader = new FileReader();
                reader.onloadend = () => {

                    this.model = reader.result
                    this.loading = false

                }

                reader.readAsDataURL(file);

                this.$emit('on-change', file)

            },
            removeAvatar() {

                this.model = null
                this.$emit('on-change', null)

            }
        }
    }
</script>