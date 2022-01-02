<template>

    <lava-tooltip :text="user.email">

        <div class="side-bar__profile"
             @click="goToRoute('detail', { id : resource.id, resource : resource.resource })">

            <img src="https://mdbootstrap.com//img/Photos/Square/1.jpg" class="side-bar__profile--avatar" alt="">

            <div class="flex flex-col w-full mx-1">

                <span>{{ user.user_type }}</span>
                <a>View profile</a>

            </div>


        </div>

    </lava-tooltip>

</template>

<script>

    export default {

        computed: {
            resource() {

                let resource = _.find(this.config.resources, {resource: this.config.config.user_resource})

                return {
                    id: this.user[resource.primaryKey],
                    resource: resource.route
                }

            }
        }

    }

</script>