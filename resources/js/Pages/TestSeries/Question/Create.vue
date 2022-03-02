<template>
    <admin-layout title="Question">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Question <span v-if="edit">Edit</span> <span v-else>Add</span>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple v-if="edit" :items="[ { route: 'question.index'}, { name:'edit'} ]" />
            <bread-simple v-else :items="[ { route: 'question.index'}, {route: 'question.create', name:'Add'} ]" />
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2">
                <form @submit.prevent=" question ? form.put(route('question.update', question.id)) : form.post(route('question.store'))">
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
                            <form-label for="title" required value="Question" />
                            <form-input id="title" type="text" required class="mt-1 w-full" v-model="form.title" autocomplete="title" />
                            <input-error :message="form.errors.title" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid gird-cols-1 mb-4 gap-4">
                        <div>
                            <form-label for="body" value="Description" />
                            <form-text-area id="body" rows="3" class="mt-1 w-full" v-model="form.body" autocomplete="body" ></form-text-area>
                            <input-error :message="form.errors.body" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid gird-cols-1 mb-4 gap-4">
                        <h2 class="text-lg border-b">Add Options <span class="inline-block text-green-500 cursor-pointer" @click="addOption"><plus-icon /></span></h2>
                        <input-error :message="form.errors.options" class="mt-2" />
                        <div class="options grid gird-cols-1 md:grid-cols-12 mb-4 gap-2" v-for="(opt, index) in form.options">

                            <div>
                                <form-label for="option_number" required value="Index" />
                                <form-input id="option_number" type="text" class="mt-1 w-full" v-model="opt.option_number" autocomplete="option_number" />
                                <input-error :message="form.errors['options.'+index+'.option_number']" class="mt-2" />
                            </div>

                            <div class="col-span-3">
                                <form-label for="option" required value="Option" />
                                <form-input id="option" type="text" class="mt-1 w-full" v-model="opt.option" autocomplete="option" />
                                <input-error :message="form.errors['options.'+index+'.option']" class="mt-2" />
                            </div>

                            <div class="col-span-6">
                                <form-label for="explanation" value="Explanation" />
                                <form-input id="explanation" type="text" class="mt-1 w-full" v-model="opt.explanation" autocomplete="explanation" />
                                <input-error :message="form.errors['options.'+index+'.explanation']" class="mt-2" />
                            </div>

                            <div class="col-span-1">
                                <form-label  value="Correct" />
                                <form-input  type="radio" :checked="opt.is_correct == 1" class="mt-1" @click="updateAns(index)" name="answer" v-model="opt.is_correct" autocomplete="is_correct" />
                                <input-error :message="form.errors['options.'+index+'.is_correct']" class="mt-2" />
                            </div>

                            <div class="col-span-1 justify-self-center flex justify-center items-center">
                                <div v-if="index == 0" class="text-green-500 cursor-pointer" @click="addOption"><plus-icon /></div>
                                <div v-else class="text-red-500 cursor-pointer" @click="removeOption(index)" ><trash-icon /></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gird-cols-1 mb-4 gap-4">

                        <div class="border-t pt-2">
                            <form-label for="note" value="Admin Note" />
                            <form-text-area id="note" rows="3" class="mt-1 w-full" v-model="form.note" autocomplete="note" ></form-text-area>
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
    import plusIcon from '@/Shared/Components/Icons/svg/Plus.vue'
    import trashIcon from '@/Shared/Components/Icons/svg/Trash.vue'
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
            FormCheckbox,
            plusIcon,
            trashIcon
        },
        props: ['question', 'classes', 'subjects', 'chapters'],
         data: () => ({
            edit: false,
            class_subjects: [],
            subject_chapters: []

         }),
        setup () {
            const form = useForm({
              chapter_id: null,
              subject_id: null,
              class_id: null,
              topic_id: null,
              title: null,
              body: null,
              note: null,
              options: [],
              active: false

            })
            return { form  }
        },

        created(){
            if (this.question) {
                Object.keys(this.question).forEach((index) => {
                    this.form[index] = this.question[index];
                });
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
            },

            addOption(){
                this.form.options.push({
                    option:null,
                    option_number: null,
                    is_correct: false,
                    explanation: null,
                });
            },

            removeOption(index) {
                if (confirm('Are you sure?')) { this.form.options.splice(index, 1); }
            },

            updateAns(index){
                this.form.options.forEach((option, key) => {
                    this.form.options[key].is_correct = 0;
                });
                this.form.options[index].is_correct = 1;
            }
        }
    })
</script>
