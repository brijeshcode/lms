<template>
    <admin-layout title="Chapter">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chapter <span v-if="edit">Edit</span> <span v-else>Add</span>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple v-if="edit" :items="[ { route: 'chapter.index'}, { name:'edit'} ]" />
            <bread-simple v-else :items="[ { route: 'chapter.index'}, {route: 'chapter.create', name:'Add'} ]" />
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2">
                <form  @submit.prevent=" chapter ? form.put(route('chapter.update', chapter.id)) : form.post(route('chapter.store'))">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">

                        <div class="control">
                            <form-label for="class_id" required value="Class" />
                            <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full" required @change="classChange($event)" v-model="form.class_id">
                                <option v-for="classin in classes" :value="classin.id">{{ classin.name }}</option>
                            </select>
                            <input-error :message="form.errors.class_id" class="mt-2" />
                        </div>

                        <div class="control" v-if="class_subjects.length > 0">
                            <form-label for="subject_id" required value="Subject" />
                            <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full " required v-model="form.subject_id">
                                <option v-for="subject in class_subjects" :value="subject.id">{{ subject.name }}</option>
                            </select>
                            <input-error :message="form.errors.subject_id" class="mt-2" />
                        </div>


                    </div>

                    <div class="grid gird-cols-1 mb-4">
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
                            <form-label for="note" value="Note" />
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
        props: ['chapter', 'classes', 'subjects'],
         data: () => ({
            edit: false,
            class_subjects: []

         }),
        setup () {
            const form = useForm({
              name: null,
              description: null,
              note: null,
              subject_id:null,
              class_id:null,
              active: false
            })
            return { form  }
        },

        created(){
            if (this.chapter) {
                Object.keys(this.chapter).forEach((index) => {
                    this.form[index] = this.chapter[index];
                });
                this.subjects.forEach(subject =>{
                    if (subject.class_id == this.chapter.subject.class_id) {this.class_subjects.push(subject);}
                });
                this.form.class_id = this.chapter.subject.class_id;
                this.edit = true;
            }
        },
        methods:{
            classChange(event){
                let class_id = event.target.value;
                this.classSubject(class_id);
            },
            classSubject(class_id){
                this.class_subjects = [];
                this.subjects.forEach(subject =>{
                    if (subject.class_id == class_id) {this.class_subjects.push(subject);}
                });
            }
        }
    })
</script>
