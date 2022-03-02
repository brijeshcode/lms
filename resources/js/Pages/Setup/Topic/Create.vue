<template>
    <admin-layout title="Topic">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Topic <span v-if="edit">Edit</span> <span v-else>Add</span>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple v-if="edit" :items="[ { route: 'topic.index'}, { name:'edit'} ]" />
            <bread-simple v-else :items="[ { route: 'topic.index'}, {route: 'topic.create', name:'Add'} ]" />
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2">
                <form @submit.prevent=" topic ? form.put(route('topic.update', topic.id)) : form.post(route('topic.store'))">
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
                            <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full " required @change="subjectChange($event)"  v-model="form.subject_id">
                                <option v-for="subject in class_subjects" :value="subject.id">{{ subject.name }}</option>
                            </select>
                            <input-error :message="form.errors.subject_id" class="mt-2" />
                        </div>

                        <div class="control" v-if="subject_chapters.length > 0">
                            <form-label for="chapter_id" required value="Chapter" />
                            <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full " required v-model="form.chapter_id">
                                <option v-for="chapter in subject_chapters" :value="chapter.id">{{ chapter.name }}</option>
                            </select>
                            <input-error :message="form.errors.chapter_id" class="mt-2" />
                        </div>

                    </div>

                    <div class="grid gird-cols-1 gap-4 mb-4">
                        <div class="control">
                            <form-label for="name" required value="Name" />
                            <form-input id="name" type="text" required class="mt-1 w-full" v-model="form.name" autocomplete="name" />
                            <input-error :message="form.errors.name" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gird-cols-1 mb-4 gap-4">
                        <div>
                            <form-label for="description" value="Description" />
                            <form-text-area id="description" type="text" rows="3" class="mt-1 w-full" v-model="form.description" autocomplete="description" />
                            <input-error :message="form.errors.description" class="mt-2" />
                        </div>

                        <div class="control">
                            <form-label for="pdf_link" required value="Pdf" />
                            <input type="file" id="pdf_link" @input="form.pdf_link = $event.target.files[0]" class="  w-full border-[1.5px] border-form-stroke rounded-lg font-medium text-body-color placeholder-body-color outline-none focus:border-primary active:border-primary transition  disabled:bg-[#F5F7FD] disabled:cursor-default cursor-pointer file:bg-[#F5F7FD] file:border-0 file:border-solid file:border-r file:border-collapse file:border-form-stroke file:py-3 file:px-5 file:mr-5 file:text-body-color file:cursor-pointer file:hover:bg-primary file:hover:bg-opacity-10" />
                            <input-error :message="form.errors.pdf_link" class="mt-2" />
                        </div>

                        <div class="control">
                            <form-label for="video_link" required value="Video Link" />
                            <form-input id="video_link" type="url" required class="mt-2 w-full" v-model="form.video_link" autocomplete="video_link" />
                            <input-error :message="form.errors.video_link" class="mt-2" />
                        </div>

                        <div class="border-t pt-2">
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
        props: ['topic', 'classes', 'subjects', 'chapters'],
         data: () => ({
            edit: false,
            class_subjects: [],
            subject_chapters: []

         }),
        setup () {
            const form = useForm({
              name: null,
              description: null,
              note: null,
              chapter_id: null,
              subject_id: null,
              class_id: null,
              video_link: null,
              pdf_link: null,
              active: false

            })
            return { form  }
        },

        created(){
            if (this.topic) {
                Object.keys(this.topic).forEach(index => this.form[index] = this.topic[index]);
                this.classSubject(this.form.class_id);
                this.subjectChapter(this.form.subject_id);
                this.edit = true;
            }
        },
        methods:{
            classChange(event){
                this.classSubject(event.target.value);
            },
            subjectChange(event){
                this.subjectChapter(event.target.value);
            },
            classSubject(class_id){
                this.class_subjects = [];
                this.subject_chapters = [];
                this.subjects.forEach(subject =>{
                    if (subject.class_id == class_id) {this.class_subjects.push(subject);}
                });
            },
            subjectChapter(subject_id){

                this.subject_chapters = [];
                this.chapters.forEach(chapter => {
                    console.log(chapter.subject_id);
                   if (chapter.subject_id == subject_id) {this.subject_chapters.push(chapter)} ;
                });
            }
        }
    })
</script>
