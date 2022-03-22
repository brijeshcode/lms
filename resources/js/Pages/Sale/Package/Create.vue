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
                            <form-label for="package-image" value="Thumbnail Image" />
                            <input id="package-image"  type="file" accept="image/png, image/jpeg" name="">
                        </div>
                    </div>

                    <div class="grid gird-cols-1 gap-4 mb-4">
                        <div class="control mb-4">
                            <form-label for="title" required value="Title" />
                            <form-input id="title" type="text" required class="mt-1 w-full" v-model="form.title" autocomplete="title" />
                            <input-error :message="form.errors.title" class="mt-2" />
                        </div>

                        <div>
                            <form-label for="description" value="Description" />
                            <form-text-area id="description" type="text" rows="3" class="mt-1 w-full" v-model.trim="form.description" autocomplete="description" />
                            <input-error :message="form.errors.description" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gird-cols-1 mb:grid-cols-5 lg:grid-cols-5 gap-4 mb-4">

                        <div class="control" >
                            <form-label for="is_free" value="Is Free" />
                            <form-checkbox name="free" @click="form.regular_price = form.sell_price = 0 " v-model:checked="form.is_free" />
                            <input-error :message="form.errors.is_free" class="mt-2" />
                        </div>
                        <div class="control" v-if="! form.is_free">
                            <form-label for="regular_price" required value="Regular Price" />
                            <form-input id="regular_price" type="number" min="0" setp="0.01" required class="mt-1 w-full" v-model.change.number="form.regular_price" autocomplete="regular_price" />
                            <input-error :message="form.errors.regular_price" class="mt-2" />
                        </div>

                        <div class="control" v-if="! form.is_free">
                            <form-label for="sell_price" required value="Sell Price" />
                            <form-input id="sell_price" type="number" min="0" setp="0.01" required class="mt-1 w-full" v-model="form.sell_price" autocomplete="sell_price" />
                            <input-error :message="form.errors.sell_price" class="mt-2" />
                        </div>
                    </div>


                    <div class="grid gird-cols-1 mb:grid-cols-5 lg:grid-cols-5 gap-4 mb-4">

                        <div class="control" >
                            <form-label for="access_forever" value="Access Forever" />
                            <form-checkbox name="access_forever" @click="form.end = null " v-model:checked="form.access_forever" />
                            <input-error :message="form.errors.access_forever" class="mt-2" />
                        </div>
                        <div class="control">
                            <form-label for="start" required value="Package Start From " />
                            <form-input id="start" type="date" required class="mt-1 w-full" v-model="form.start" autocomplete="start" />
                            <input-error :message="form.errors.start" class="mt-2" />
                        </div>

                        <div class="control" v-if="! form.access_forever">
                            <form-label for="end" value="Package End To" />
                            <form-input id="end" type="date"  class="mt-1 w-full" v-model="form.end" autocomplete="end" />
                            <input-error :message="form.errors.end" class="mt-2" />
                        </div>
                    </div>

                    <div class="my-4">
                        <div class="border mb-4 rounded p-2 bg-gray-100">
                            Package Items:
                            <select v-model="current_package_type">
                                <option v-for="(packateType) in packageTypes" :value="packateType" v-text="packateType"></option>
                            </select>
                            <simple-button @click="addPackageItem" class="ml-4 hover:bg-indigo-800 bg-blue-500" type="button" >Add</simple-button>
                        </div>

                        <div v-if="form.test_serires.length > 0" class="border rounded mb-4">
                            <div class="heading bg-gray-100 p-2">
                                <h2 class="uppercase inline-block ">Package test series</h2>
                                <simple-button @click="addTestSeriesItem" class="ml-4 bg-green-500 hover:bg-green-800" type="button" >More</simple-button>
                                <simple-button @click="removePackageItem('test_serires')" class="ml-4 bg-red-500 hover:bg-red-800" type="button" >Remove</simple-button>
                            </div>
                            <div class="p-2">
                                <div v-for="(testItem, index) in form.test_serires" class="item border rounded bg-gray-50 mb-2 p-2 grid grid-cols-12 gap-4 hover:bg-gray-100">
                                    <div class="col-span-6">
                                        <select v-model="testItem.test_series_id" class="w-full" >
                                            <option v-for="test in testSeries" :value="test.id">{{ test.title }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-4">
                                        <form-text-area id="description" type="text" rows="2" class="mt-1 w-full" v-model="testItem.description" placeholder="Add Description to show in fron end" autocomplete="description" />
                                    </div>
                                    <div>
                                        <form-label value="Free" />
                                        <form-checkbox name="free" v-model:checked="testItem.is_free" />
                                    </div>
                                    <div>
                                        <div @click="removePackageItem('test_serires', index)" class="cursor-pointer" ><icon-remove class="text-white" /></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="form.live_classes.length > 0" class="border rounded mb-4">
                            <div class="heading bg-gray-100 p-2">
                                <h2 class="uppercase inline-block ">Package Live classes</h2>
                                <simple-button @click="addLiveClassesItem" class="ml-4 bg-green-500 hover:bg-green-800" type="button" >More</simple-button>
                                <simple-button @click="removePackageItem('live_classes')" class="ml-4 bg-red-500 hover:bg-red-800" type="button" >Remove</simple-button>
                            </div>
                            <div class="p-2">
                                <div v-for="(liveClassItem, index) in form.live_classes" class="item border rounded bg-gray-50 mb-2 p-2 grid grid-cols-12 gap-4 hover:bg-gray-100">
                                    <div class="col-span-6">
                                        <select v-model="liveClassItem.live_class_id" class="w-full" >
                                            <option v-for="liveClass in liveClasses" :value="liveClass.id">{{ liveClass.title }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-4">
                                        <form-text-area id="description" type="text" rows="2" class="mt-1 w-full" v-model="liveClassItem.description" placeholder="Add Description to show in fron end" autocomplete="description" />
                                    </div>

                                    <div>
                                        <div @click="removePackageItem('live_classes', index)" class="cursor-pointer" ><icon-remove class="text-white" /></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="form.recorded_classes.length > 0" class="border rounded mb-4">
                            <div class="heading bg-gray-100 p-2">
                                <h2 class="uppercase inline-block ">Package Recorded classes</h2>
                                <simple-button @click="addRecordedClassesItem" class="ml-4 bg-green-500 hover:bg-green-800" type="button" >More</simple-button>
                                <simple-button @click="removePackageItem('recorded_classes')" class="ml-4 bg-red-500 hover:bg-red-800" type="button" >Remove</simple-button>
                            </div>
                            <div v-for="(recordedClassItem, index) in form.recorded_classes" class="item border rounded bg-gray-50 mb-2 p-2 grid grid-cols-12 gap-4 hover:bg-gray-100">
                                <div class="col-span-4">
                                    <select v-model="recordedClassItem.recorded_class_id" class="w-full" >
                                        <option v-for="recorcedClass in recordedClasses" :value="recorcedClass.id">{{ recorcedClass.name }}</option>
                                    </select>
                                </div>
                                <div class="col-span-4">
                                    <form-text-area id="description" type="text" rows="2" class="mt-1 w-full" v-model="recordedClassItem.description" placeholder="Add Description to show in fron end" autocomplete="description" />
                                </div>
                                <div>
                                    <div @click="removePackageItem('recorded_classes', index)" class="cursor-pointer" ><icon-remove /></div>
                                </div>
                            </div>
                        </div>

                        <div v-if="form.recorded_subjects.length > 0" class="border rounded mb-4">
                            <div class="heading bg-gray-100 p-2">
                                <h2 class="uppercase inline-block ">Package Recorded Subjects</h2>
                                <simple-button @click="addRecordedSubjectsItem" class="ml-4 bg-green-500 hover:bg-green-800" type="button" >More</simple-button>
                                <simple-button @click="removePackageItem('recorded_subjects')" class="ml-4 bg-red-500 hover:bg-red-800" type="button" >Remove</simple-button>
                            </div>
                            <div class="p-2">
                                <div v-for="(recordSubjectItem, index) in form.recorded_subjects" class="item border rounded bg-gray-50 mb-2 p-2 grid grid-cols-12 gap-4 hover:bg-gray-100">
                                    <div class="col-span-3">
                                        <select v-model="recordSubjectItem.recorded_class_id" @change="classChange($event)" class="w-full" >
                                            <option disabled>Select Class</option>
                                            <option v-for="recorcedClass in recordedClasses" :value="recorcedClass.id">{{ recorcedClass.name }}</option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <select v-model="recordSubjectItem.recorded_subject_id" @change="subjectChange($event, index)" class="w-full" >
                                            <option disabled>Select Subject</option>
                                            <option v-for="recordedSubject in classSubjects" :value="recordedSubject.id">{{ recordedSubject.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-span-4">
                                        <form-text-area id="description" type="text" rows="2" class="mt-1 w-full" v-model="recordSubjectItem.description" placeholder="Add Description to show in fron end" autocomplete="description" />
                                    </div>
                                    <div>
                                        <div @click="removePackageItem( 'recorded_subjects' , index)" class="cursor-pointer" ><icon-remove /></div>
                                    </div>

                                    <div class="chapter col-span-6">
                                        <table>
                                            <tr>
                                                <th>Chapter Title</th>
                                                <th>Free / Demo</th>
                                            </tr>
                                            <tr v-for="chappter in recordSubjectItem.chapters" >
                                                <td>{{ chappter.name }}</td>
                                                <td><form-checkbox name="free" v-model:checked="chappter.is_free" /></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
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
    import IconRemove from '@/Shared/Components/Icons/svg/Trash.vue'
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
            IconRemove
        },
        props: ['package', 'testSeries', 'liveClasses', 'subjects', 'chapters', 'recordedClasses'],
         data: () => ({
            edit: false,
            current_package_type: null,
            classSubjects: [],
            subjectChapters: [],
            packageTypes : [ 'Test Series','Live classes', 'Recorded classes','Recorded subjects']
         }),
        setup () {
            const form = useForm({
              title: null,
              start: new Date().toISOString().slice(0,10),
              end: null,
              description: null,
              image: null,
              regular_price: 0,
              sell_price: 0,
              is_free: false,
              note: null,
              active: false,
              test_serires : [],
              live_classes : [],
              recorded_classes : [],
              recorded_subjects : []
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
            addPackageItem(){

                if(this.current_package_type == null){
                    alert('Select package item first');
                }else if (this.current_package_type == 'Test Series') {
                    this.addTestSeriesItem();
                }else if(this.current_package_type == 'Live classes'){
                    this.addLiveClassesItem();
                }else if(this.current_package_type == 'Recorded classes'){
                    this.addRecordedClassesItem();
                }else if(this.current_package_type == 'Recorded subjects'){
                    this.addRecordedSubjectsItem();
                }
            },
            removePackageItem(removeMe, index = ''){
                if (confirm('Are you sure you want to remove this item?')) {
                    if (index !== '') {
                        this.form[removeMe].splice(index, 1);
                    }else{
                        this.form[removeMe] = [];
                    }
                }
            },
            addTestSeriesItem(){
                this.form.test_serires.push({
                    test_series_id: null,
                    description: null,
                    is_free: false
                });
            },
            addLiveClassesItem(){
                this.form.live_classes.push({
                    live_class_id: null,
                    description: null
                });
            },
            addRecordedClassesItem(){
                this.form.recorded_classes.push({
                    recorded_class_id: null,
                    description: null
                });
            },
            addRecordedSubjectsItem(){
                this.form.recorded_subjects.push({
                    recorded_class_id: null,
                    recorded_subject_id: null,
                    description: null,
                    chapters : []
                });
            },
            classChange(event, index){
                this.classSubjects = this.subjects.filter(subject => subject.class_id == event.target.value);
            },
            subjectChange(event, index){
                this.form.recorded_subjects[index].chapters = [];
                let chapters = this.chapters.filter(chapter => chapter.subject_id == event.target.value);

                chapters.forEach((chapter , key) => {
                    // this.form.recorded_subjects[index].chapters[key].is_free   = false;
                    this.form.recorded_subjects[index].chapters.push({
                        class_id : chapter.class_id,
                        name: chapter.name,
                        subject_id : chapter.subject_id,
                        chapter_id : chapter.id,
                        is_free : false,
                    });
                });

                console.log(this.form.recorded_subjects[index].chapters);
            }
        }
    })
</script>
