<template>
    <admin-layout title="Classes">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Classes
                <add-link createRoute="studentclasses.create" isbutton >Add</add-link>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple :items="[ { route: 'studentclasses.index', name: 'Class'} ]" />
        </template>

        <template #actions>
            <div class="flex">
              <search searchRoute='studentclasses.index' />
              <Add-link createRoute="studentclasses.create" withIcon  />
            </div>
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <!-- This example requires Tailwind CSS v2.0+ -->
            <div class="flex flex-col">
              <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                  <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="table-auto min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                             Admin Note
                          </th>
                          <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="classData in classes.data" :key="classData.id" class="hover:bg-gray-100">
                          <td class="px-4 py-4 whitespace-nowrap">
                            <Edit-link :edit="{route: 'studentclasses.edit', to:classData.id }"  >
                              <div class="text-sm text-gray-900">{{ classData.name }}</div>
                            </Edit-link>
                            <span v-if="classData.active" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                              Active
                            </span>
                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                              in-Active
                            </span>
                          </td>
                          <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="text-sm text-gray-500">{{ classData.description }}</div>
                          </td>
                          <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="text-sm text-gray-500">{{ classData.note }}</div>
                          </td>
                          <td  class="px-4 py-4 whitespace-nowrap  text-sm flex justify-end text-right  font-medium">
                            <!-- <Link :href="route('subject.create')" title="Add subject" class="text-green-600" ><subject-add /></Link> -->
                            <Edit-link  :edit="{route: 'studentclasses.edit', to:classData.id }" showicon />
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <Pagination :pageData="classes" pageof=" Classes" />
                  </div>
                </div>
              </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AdminLayout from '@/Shared/Layouts/AdminLayout.vue'
    import Pagination from '@/Shared/Components/Pagination/Simple.vue'
    import BreadSimple from '@/Shared/Components/Breadcrum/Simple.vue'
    import AddLink from '@/Shared/Components/Links/Add.vue'
    import EditLink from '@/Shared/Components/Links/Edit.vue'
    import Search from '@/Shared/Components/Filters/Search.vue'
    import SubjectAdd from '@/Shared/Components/Icons/svg/FolderAdd.vue'
    import { Link } from '@inertiajs/inertia-vue3';
    // import AlertSuccess from '@/Shared/Components/Alerts/Pops/Success.vue'

    export default defineComponent({
        components: {
          AdminLayout,BreadSimple, Search,AddLink,EditLink,Pagination,SubjectAdd,Link
        },
        props:{
            classes: Object,
        }
    })
</script>