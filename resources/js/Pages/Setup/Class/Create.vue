<template>
    <admin-layout title="Class">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Class <span v-if="edit">Edit</span> <span v-else>Add</span>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple v-if="edit" :items="[ { route: 'studentclasses.index', name: 'class'}, { name:'edit'} ]" />
            <bread-simple v-else :items="[ { route: 'studentclasses.index', name: 'class'}, {route: 'studentclasses.create', name:'Add'} ]" />
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2">
                <form  @submit.prevent=" studentclass ? form.put(route('studentclasses.update', studentclass.id)) : form.post(route('studentclasses.store'))">
                    <div class="grid grid-cols-1 gap-4 mb-4">

                        <div class="control">
                            <form-label for="name" required value="Name" />
                            <form-input id="name" type="text" required class="mt-1 w-full" v-model="form.name" autocomplete="name" />
                            <input-error :message="form.errors.name" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gird-cols-1 mb-4">
                        <div>
                            <form-label for="description" value="Description" />
                            <form-text-area id="description" type="text" rows="3" class="mt-1 w-full" v-model="form.description" autocomplete="description" />
                            <input-error :message="form.errors.description" class="mt-2" />
                        </div>

                        <div>
                            <form-label for="note" value="Admin Note" />
                            <form-text-area id="note" type="text" rows="3" class="mt-1 w-full" v-model="form.note" autocomplete="note" />
                            <input-error :message="form.errors.note" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex flex-row mb-4">
                        <div class="">
                            <label class="flex items-center">
                                <form-checkbox name="active" v-model:checked="form.active" />
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <simple-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Save</simple-button>
                    </div>
                </form>
            </div>
        </div>

    </admin-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import { useForm } from '@inertiajs/inertia-vue3'
    import AdminLayout from '@/Shared/Layouts/AdminLayout.vue'
    import SimpleButton from '@/Shared/Components/Form/Simple/Button.vue'
    import FormInput from '@/Shared/Components/Form/Simple/Input.vue'
    import FormTextArea from '@/Shared/Components/Form/Simple/Textarea.vue'
    import FormLabel from '@/Shared/Components/Form/Simple/Label.vue'
    import InputError from '@/Shared/Components/Form/Simple/InputError.vue'
    import FormCheckbox from '@/Shared/Components/Form/Simple/Checkbox.vue'
    import BreadSimple from '@/Shared/Components/Breadcrum/Simple.vue'
    // import { Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {
            InputError,
            FormTextArea,
            FormInput,
            FormLabel,
            BreadSimple,
            AdminLayout,
            SimpleButton,
            FormCheckbox
        },
        props: ['studentclass'],
         data: () => ({
            edit: false,

         }),
        setup () {
            const form = useForm({
              name: null,
              description: null,
              note: null,
              active: false
            })
            return { form  }
        },

        created(){
            if (this.studentclass) {
                Object.keys(this.studentclass).forEach(index => this.form[index] = this.studentclass[index]);
                this.edit = true;
            }
        },
        methods:{

        }
    })
</script>
