<template>
    <admin-layout title="Live Class">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Live Class <span v-if="edit">Edit</span> <span v-else>Add</span>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple v-if="edit" :items="[ { route: 'liveClass.index', name:'Live Class'}, { name:'edit'} ]" />
            <bread-simple v-else :items="[ { route: 'liveClass.index', name:'Live Class'}, {route: 'liveClass.create', name:'Add'} ]" />
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2">
                <form  @submit.prevent=" liveClass ? form.put(route('liveClass.update', liveClass.id)) : form.post(route('liveClass.store'))">
                    <div class="grid gird-cols-1 mb-4">
                        <div>
                            <form-label for="liveClass-image" value="Thumbnail Image" />
                            <input id="liveClass-image"  type="file" accept="image/png, image/jpeg" name="">
                        </div>
                    </div>
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
        props: ['liveClass', 'classes', 'subjects', 'chapters'],

         data: () => ({
            edit: false,
            class_subjects: [],
            subject_chapters: []
         }),
        setup () {
            const form = useForm({
              class_id: null,
              subject_id: null,
              chapter_id: null,
              class_id: null,
              title: null,
              description: null,
              image: null,
              is_free: false,
              note: null,
              active: false
            })
            return { form  }
        },

        created(){
            if (this.liveClass) {
                Object.keys(this.liveClass).forEach(index => this.form[index] = this.liveClass[index]);
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
                this.chapters.forEach(chapter => (chapter.subject_id == subject_id) ? this.subject_chapters.push(chapter) : '' );
            }




        }
    })
</script>
