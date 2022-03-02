<template>
    <admin-layout title="Test Series">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Test Series <span v-if="edit">Edit</span> <span v-else>Add</span>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple v-if="edit" :items="[ { route: 'testseries.index'}, { name:'edit'} ]" />
            <bread-simple v-else :items="[ { route: 'testseries.index'}, {route: 'testseries.create', name:'Add'} ]" />
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2">
                <form @submit.prevent=" testseries ? form.put(route('testseries.update', testseries.id)) : form.post(route('testseries.store'))">
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

                        <div>
                            <form-label for="time_duration" required value="Time Duration" />
                            <form-input id="time_duration" type="text" class="mt-1 w-full" v-model="form.time_duration" autocomplete="time_duration" />
                            <input-error :message="form.errors.time_duration" class="mt-2" />
                        </div>

                        <div>
                            <form-label for="display_instant_result" required value="Display Instant Result" />
                            <form-input id="display_instant_result" type="text" class="mt-1 w-full" v-model="form.display_instant_result" autocomplete="display_instant_result" />
                            <input-error :message="form.errors.display_instant_result" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gird-cols-1 gap-4 mb-4">
                        <div class="control">
                            <form-label for="title" required value="Test Series Title" />
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
                        <h2 class="text-lg border-b">Add Questions <span class="inline-block text-green-500 cursor-pointer" @click="addQuestions"><plus-icon /></span></h2>
                        <input-error :message="form.errors.questions" class="mt-2" />
                        <div class="questions grid gird-cols-1 md:grid-cols-12 mb-4 gap-2 justify-items-stretch content-center " v-for="(question, index) in form.questions">
                            <div>
                                <form-label :for="'question_index_'+ index" required value="Que. #" />
                                <form-input :id="'question_index_'+ index" type="text" class="mt-1 w-full" v-model="question.question_index" autocomplete="question_index" />
                                <input-error :message="form.errors['questions.'+index+'.question_index']" class="mt-2" />
                            </div>

                            <div class="col-span-8 ">
                                <form-label :for="'question_id'+ index" required value="Question" />
                                <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full" :id="'question_id'+ index" required v-model="question.question_id">
                                    <option v-for="testseries in testseries_questions" :value="testseries.id">{{ testseries.title }}</option>
                                </select>
                                <input-error :message="form.errors['questions.'+index+'.question_id']" class="mt-2" />
                            </div>

                            <div>
                                <form-label :for="'positive_mark_'+ index" class="text-green-500" required value=" + Marks" />
                                <form-input :id="'positive_mark_'+ index" type="text" class="mt-1 w-full" v-model="question.positive_mark" autocomplete="positive_mark" />
                                <input-error :message="form.errors['questions.'+index+'.positive_mark']" class="mt-2" />
                            </div>

                            <div>
                                <form-label :for="'negative_mark_'+ index" required class="text-red-500"  value=" - Marks" />
                                <form-input :id="'negative_mark_'+ index" type="text" class="mt-1 w-full" v-model="question.negative_mark" autocomplete="negative_mark" />
                                <input-error :message="form.errors['questions.'+index+'.negative_mark']" class="mt-2" />
                            </div>

                            <div class="col-span-1 justify-self-center flex justify-center items-center">
                                <div v-if="index == 0" class="text-green-500 cursor-pointer" @click="addQuestions"><plus-icon /></div>
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
        props: ['testseries', 'classes', 'subjects', 'chapters','questions'],
         data: () => ({
            edit: false,
            class_subjects: [],
            subject_chapters: [],
            testseries_questions : []

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
              display_instant_result:false,
              time_duration:'0:00',
              questions: [],
              active: false
            })
            return { form  }
        },

        created(){
            if (this.testseries) {
                Object.keys(this.testseries).forEach((index) => {
                    this.form[index] = this.testseries[index];
                });
                this.classSubject(this.form.class_id);
                this.subjectChapter(this.form.subject_id);
                this.subjectQuestion(this.form.subject_id);
                this.edit = true;
            }
        },
        methods:{
            classChange(event){
                this.classSubject(event.target.value);
            },
            subjectChange(event){
                this.subjectChapter(event.target.value);
                this.subjectQuestion(event.target.value);
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
                   if (chapter.subject_id == subject_id) {this.subject_chapters.push(chapter)};
                });
            },

            /*chapterQuestion(chapter_id){

                this.testseries_questions = [];
                this.questions.forEach(question => {
                   if (question.chapter_id == chapter_id) {this.testseries_questions.push(question)};
                });
            },*/

            subjectQuestion(subject_id){

                this.testseries_questions = [];
                this.questions.forEach(question => {
                   if (question.subject_id == subject_id) {this.testseries_questions.push(question)};
                });
                if(this.testseries_questions.length == 0){alert('This subject dont have any questions.')}
            },

            addQuestions(){
                if (this.testseries_questions.length == 0) { alert('Select testseries subject.') ;return ; }
                this.form.questions.push({
                    question_index:null,
                    question_id: null,
                    positive_mark: 0,
                    negative_mark: 0,
                });
            },

            removeOption(index) {
                if (confirm('Are you sure?')) { this.form.questions.splice(index, 1); }
            },

            updateAns(index){
                this.form.questions.forEach((option, key) => {
                    this.form.questions[key].is_correct = 0;
                });
                this.form.questions[index].is_correct = 1;
            }
        }
    })
</script>
