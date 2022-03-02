<template>
    <admin-layout title="Package">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Package <span v-if="edit">Edit</span> <span v-else>Add</span>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple v-if="edit" :items="[ { route: 'package.index'}, { name:'edit'} ]" />
            <bread-simple v-else :items="[ { route: 'package.index'}, {route: 'package.create', name:'Add'} ]" />
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2">
                <form  @submit.prevent=" package ? form.put(route('package.update', package.id)) : form.post(route('package.store'))">
                    <div class="grid gird-cols-1 mb-4">
                        <div>
                            <form-label for="package-image" value="Image" />
                            <input id="package-image" type="file" name="">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mb-4">

                        <div class="control">
                            <form-label for="title" required value="Title" />
                            <form-input id="title" type="text" required class="mt-1 w-full" v-model="form.title" autocomplete="title" />
                            <input-error :message="form.errors.title" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gird-cols-1 mb-4">
                        <div>
                            <form-label for="description" value="Description" />
                            <form-text-area id="description" type="text" rows="3" class="mt-1 w-full" v-model="form.description" autocomplete="description" />
                            <input-error :message="form.errors.description" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gird-cols-1 mb:grid-cols-5 lg:grid-cols-5 gap-4 mb-4">

                        <div class="control" >
                            <form-label for="is_free" required value="Free access" />
                            <form-checkbox name="free" @click="form.regular_price = form.sell_price = 0 " v-model:checked="form.is_free" />
                            <input-error :message="form.errors.is_free" class="mt-2" />
                        </div>
                        <div class="control" v-if="! form.is_free">
                            <form-label for="regular_price" required value="Regular Price" />
                            <form-input id="regular_price" type="number" min="0" setp="0.01" required class="mt-1 w-full" v-model="form.regular_price" autocomplete="regular_price" />
                            <input-error :message="form.errors.regular_price" class="mt-2" />
                        </div>

                        <div class="control" v-if="! form.is_free">
                            <form-label for="sell_price" required value="Sell Price" />
                            <form-input id="sell_price" type="number" min="0" setp="0.01" required class="mt-1 w-full" v-model="form.sell_price" autocomplete="sell_price" />
                            <input-error :message="form.errors.sell_price" class="mt-2" />
                        </div>



                    </div>

                    <div class="grid gird-cols-1 mb-4">
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
        props: ['package'],
         data: () => ({
            edit: false,

         }),
        setup () {
            const form = useForm({
              title: null,
              description: null,
              image: null,
              regular_price: 0,
              sell_price: 0,
              is_free: false,
              note: null,
              active: false
            })
            return { form  }
        },

        created(){
            if (this.package) {
                Object.keys(this.package).forEach(index => this.form[index] = this.package[index]);
                this.edit = true;
            }
        },
        methods:{

        }
    })
</script>
