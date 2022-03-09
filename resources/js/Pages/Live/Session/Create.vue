<template>
    <admin-layout title="Live Session">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Live Session <span v-if="edit">Edit</span> <span v-else>Add</span>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple v-if="edit" :items="[ { route: 'liveSession.index', name:'Live Session'}, { name:'edit'} ]" />
            <bread-simple v-else :items="[ { route: 'liveSession.index', name:'Live Session'}, {route: 'liveSession.create', name:'Add'} ]" />
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2">
                <form  @submit.prevent=" liveSession ? form.put(route('liveSession.update', liveSession.id)) : form.post(route('liveSession.store'))">

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">

                        <div class="control">
                            <form-label for="class_id" required value="Live Class" />
                            <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full" required v-model="form.live_class_id">
                                <option v-for="classin in liveClasses" :value="classin.id">{{ classin.title }}</option>
                            </select>
                            <input-error :message="form.errors.class_id" class="mt-2" />
                        </div>

                        <div>
                            <form-label for="date" required value="Date" />
                            <form-input id="date" type="date" required class="mt-1 w-full" v-model.trim="form.date" />
                            <input-error :message="form.errors.date" class="mt-2" />
                        </div>

                        <div>
                            <form-label for="timing" required value="Timing" />
                            <form-input id="timing" required type="time" class="mt-1 w-full" v-model.trim="form.timing" />
                            <input-error :message="form.errors.timing" class="mt-2" />
                        </div>

                        <div class="col-span-2">
                            <form-label for="link" required value="Link" />
                            <form-input id="link" required type="url" placeholder="https://expample.com" class="mt-1 w-full" v-model.trim="form.link" />
                            <input-error :message="form.errors.link" class="mt-2" />
                        </div>
                    </div>

                   
                    <div class="grid gird-cols-1 mb-4">
                        <div>
                            <form-label for="description" value="Other Session Note" />
                            <form-text-area id="description" type="text" rows="3" class="mt-1 w-full" v-model.trim="form.description" autocomplete="description" />
                            <input-error :message="form.errors.description" class="mt-2" />
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
        props: ['liveClasses', 'liveSession'],

         data: () => ({
            edit: false,
            class_subjects: [],
            subject_chapters: []
         }),
        setup () {
            const form = useForm({
              live_class_id: null,
              date: null,
              timing: null,
              link: null,
              note: null,
              description: null,
              active: false
            })
            return { form  }
        },

        created(){
            if (this.liveSession) {
                Object.keys(this.liveSession).forEach(index => this.form[index] = this.liveSession[index]);
                this.edit = true;
            }
        },
        methods:{

        }
    })
</script>
