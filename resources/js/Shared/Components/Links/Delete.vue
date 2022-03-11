<template>
    <span @click="deleteMe" title="Delete" class="text-red-600 hover:text-red-900">
        <span v-if="icon"><icon /></span>
        <span v-else><slot></slot></span>
    </span>

</template>

<script>
    import icon from '@/Shared/Components/Icons/svg/Trash.vue'
    import { Inertia } from '@inertiajs/inertia'
    import { Link } from '@inertiajs/inertia-vue3';
    export default{
        components:{Link,icon},
        props: {
            link: Object,
            icon: {type: Boolean , default: false},
            static: {type: Boolean , default: true}
        },
        methods: {
            deleteMe(){
                if(!confirm('are you sure')){ return };
                Inertia.delete(route(this.link.route, this.link.id),{
                    onProgress: progress => {console.log('deleteing')},
                });
            }
        }
    }
</script>