<template>
    <admin-layout title="User Add">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User <span v-if="edit">Edit</span> <span v-else>Add</span>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple v-if="edit" :items="[ { route: 'users.index'}, { name:'edit'} ]" />
            <bread-simple v-else :items="[ { route: 'users.index'}, {route: 'users.create', name:'Add'} ]" />
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2">
                <form  @submit.prevent=" editUser ? form.put(route('users.update', editUser.id)) : form.post(route('users.store'))">
                    <div class="flex flex-row mb-4">

                        <div class="basis-1/4">
                            <jet-label for="name" required value="Name" />
                            <jet-input id="name" type="text" class="mt-1 block" v-model="form.name" autocomplete="name" />
                            <jet-input-error :message="form.errors.name" class="mt-2" />
                        </div>

                        <div class="basis-1/4">
                            <jet-label for="email" required value="Email"/>
                            <jet-input id="email" type="text" class="mt-1 block" v-model="form.email" autocomplete="email" />
                            <jet-input-error :message="form.errors.email" class="mt-2" />
                        </div>

                        <div class="basis-1/4">
                            <jet-label v-if="edit" for="password" value="New Password" />
                            <jet-label v-else for="password" required value="New Password" />
                            <jet-input id="password" type="password" class="mt-1 block" v-model="form.password" autocomplete="password" />
                            <jet-input-error :message="form.errors.state" class="mt-2" />
                        </div>


                    </div>
                    <div class="flex flex-row mb-4">
                        <div class="mb-4 basis-1/4">
                            <jet-label for="role" required value="Role" />
                            <select id="role"  v-model="form.role" class="w-60 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block capitalize" >
                                <option v-for="role in roles" :value="role.id">{{ role.name }}</option>
                            </select>
                            <jet-input-error :message="form.errors.role" class="mt-2" />
                        </div>

                        <div class=" basis-1/2">
                            <jet-label for="note" value="Note" />
                            <jet-input id="note" type="text" class="mt-1 block" v-model="form.note" autocomplete="note" />
                            <jet-input-error :message="form.errors.note" class="mt-2" />
                        </div>



                    </div>

                    <!-- <div class="text-xl uppercase  mb-4 mt-4">
                        Permissions
                        <div class="h-0.5 w-20 bg-gray-500 rounded"></div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class=" bg-indigo-500  p-4" v-for="(permission, index) in permissions">
                            <label class="flex text-white items-center">
                                <input type="checkbox" :id="permission.id" :value="permission.id" v-model="form.permissions">
                                <span class="ml-2 font-bold capitalize">{{ permission.name }}</span>
                            </label>
                        </div>
                    </div> -->

                    <div class="flex flex-row mb-4">
                        <div class="">
                            <label class="flex items-center">
                                <jet-checkbox name="active" v-model:checked="form.active" />
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Save</jet-button>
                    </div>
                </form>
            </div>
        </div>

    </admin-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AdminLayout from '@/Shared/Layouts/AdminLayout.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import { useForm } from '@inertiajs/inertia-vue3'
    import JetCheckbox from '@/Jetstream/Checkbox.vue'
    import BreadSimple from '@/Shared/Components/Breadcrum/Simple.vue'
    import RemoveIcon from '@/Shared/Components/Icons/svg/Trash.vue'
    // import { Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {
            JetInputError,
            BreadSimple,
            JetInput,
            AdminLayout,
            JetButton,
            JetLabel,
            JetCheckbox,
            RemoveIcon
        },
        props: ['editUser', 'roles', 'permissions'],
         data: () => ({
            edit: false
         }),
        setup () {
            const form = useForm({
              name: null,
              email: '',
              password: '',
              note: '',
              // permissions: [],
              role: '2',
              active: false
            })

            return { form  }
        },

        created(){
            if (this.editUser) {
                Object.keys(this.editUser).forEach((index) => {
                    this.form[index] = this.editUser[index];
                });
                this.form.role = this.editUser.role.id;
                this.edit = true;
            }
        },
        methods:{

            roleChange(event){
                let role = {};
                this.form.permissions = [];
                this.roles.forEach((role, index) =>{
                    if (role.id == event.target.value ) {
                        role.permissions.forEach(permission => {
                            this.form.permissions.push(permission.id);
                        });
                    }
                });
            }
        }
    })
</script>
