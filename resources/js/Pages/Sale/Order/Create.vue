<template>
    <admin-layout title="Order">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Order <span v-if="edit">Edit</span> <span v-else>Add</span>
            </h2>
        </template>

        <template #breadcrum>
            <bread-simple v-if="edit" :items="[ { route: 'order.index'}, { name:'edit'} ]" />
            <bread-simple v-else :items="[ { route: 'order.index'}, {route: 'order.create', name:'Add'} ]" />
        </template>


        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2">
                <form  @submit.prevent=" order ? form.put(route('order.update', order.id)) : form.post(route('order.store'))">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">
                        <div class="control">
                            <form-label for="date" required value="Date" />
                            <form-input id="date" type="date" required class="mt-1 w-full" v-model="form.date" autocomplete="date" />
                            <input-error :message="form.errors.date" class="mt-2" />
                        </div>
                        <div class="control">
                            <form-label for="customer_id" required value="Customer" />
                            <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full" required v-model="form.customer_id">
                                <option v-for="customer in customers" :value="customer.id">{{ customer.name }} | {{ customer.email }}</option>
                            </select>
                            <input-error :message="form.errors.customer_id" class="mt-2" />
                        </div>

                        <div class="control">
                            <form-label for="status" required value="Status" />
                            <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full capitalize" required v-model="form.status">
                                <option v-for="status in order_status" :value="status">{{ status }}</option>
                            </select>
                            <input-error :message="form.errors.status" class="mt-2" />
                        </div>

                    </div>
                    <div class="my-4">
                        <div class="border rounded mb-4">
                            <div class="heading bg-gray-100 p-2">
                                <h2 class="inline-block ">Products (Packages)</h2>
                                <simple-button @click="addProduct" class="ml-4 hover:bg-indigo-800 bg-blue-500" type="button" >Add</simple-button>
                            </div>
                            <div class="p-2">
                                <div v-for="(product, index) in form.products" class="item border rounded bg-gray-50 mb-2 p-2 grid grid-cols-12 gap-4 hover:bg-gray-100">
                                    <div class="col-span-4">
                                        <form-label for="product_id" required value="Product" />
                                            <select class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full" v-model="product.product_id" required @change="productChange(index)">
                                                <option v-for="packageItem in packages" :value="packageItem.id">{{ packageItem.title }}</option>
                                            </select>
                                        <input-error :message="form.errors['products.'+index+'.product_id']" class="mt-2" />
                                    </div>
                                    <div class="col-span-2">
                                        <form-label for="'quantity_'+ index" required value="Quantity" />
                                        <form-input id="'quantity_'+ index" type="number" min="0" required class="mt-1 w-full" v-model="product.quantity" @keyup="quantityChange(index)" />
                                        <input-error :message="form.errors['products.'+index+'.quantity']" class="mt-2" />
                                    </div>
                                    <div class="col-span-2">
                                        <form-label for="" required value="Rate" />
                                        <div class="text-sm text-gray-500" v-if="product.sell_price > 0">
                                          <span class="inline-block"> <rupee-icon class="inline w-4 h-4" />{{ product.sell_price }}</span> <span class="line-through">{{ product.regular_price }}</span> </div>
                                        <div class="text-sm text-gray-500" v-else><rupee-icon class="inline w-4 h-4"  />{{ product.regular_price }}</div>
                                        <input-error :message="form.errors.name" class="mt-2" />
                                    </div>
                                    <div class="col-span-2">
                                        <form-label for="" required value="Total" />
                                        {{ product.total }}
                                        <input-error :message="form.errors.name" class="mt-2" />
                                    </div>
                                    <div>
                                        <div @click="removePackageItem(index)" class="cursor-pointer" ><icon-remove class="text-white" /></div>
                                    </div>
                                </div>

                                <div v-if="form.products.length > 0" class="item border rounded bg-gray-50 mb-2 p-2 grid grid-cols-12 gap-4 hover:bg-gray-100">
                                    <div class="col-span-4">

                                    </div>
                                    <div class="col-span-2">
                                        {{ form.quantity }}
                                    </div>
                                    <div class="col-span-2">

                                    </div>
                                    <div class="col-span-2">
                                        {{ form.total }}
                                    </div>
                                    <div>

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
    import RupeeIcon from '@/Shared/Components/Icons/svg/Rupee.vue'
    // import { Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {
            InputError,
            IconRemove,
            RupeeIcon,
            FormTextArea,
            FormInput,
            FormLabel,
            BreadSimple,
            AdminLayout,
            SimpleButton,
            FormCheckbox
        },
        props: ['order', 'customers', 'packages'],
         data: () => ({
            edit: false,
            order_status: ['pending', 'processing', 'completed', 'returned', 'cancelled']

         }),
        setup () {
            const form = useForm({
              date: null,
              status: null,
              customer_id: null,
              quantity:0,
              discount:0,
              sub_total: 0,
              total: 0,
              note: null,
              products : []
            })
            return { form  }
        },

        created(){
            if (this.order) {
                Object.keys(this.order).forEach(index => this.form[index] = this.order[index]);
                this.form.products.forEach((product, index) => this.form.products[index].total = this.form.products[index].sell_price > 0 ?  this.form.products[index].quantity * this.form.products[index].sell_price : this.form.products[index].quantity * this.form.products[index].regular_price);
                this.edit = true;
            }
        },
        methods:{
            addProduct(){
                this.form.products.push({
                    product_id: null,
                    product_title: null,
                    regular_price: 0,
                    sell_price: 0,
                    quantity: 0,
                    total: 0
                });
            },
            productChange(index){
                let product = this.packages.filter(item => item.id == this.form.products[index].product_id)[0];
                let quantity = this.form.products[index].quantity;
                this.form.products[index].product_title = product.title;
                this.form.products[index].regular_price = product.regular_price;
                this.form.products[index].sell_price = product.sell_price;
                this.quantityChange(index);

            },
            quantityChange(index){
                let quantity = this.form.products[index].quantity;
                let product = this.packages.filter(item => item.id == this.form.products[index].product_id)[0];
                this.form.products[index].total = product.sell_price > 0 ?  quantity * product.sell_price : quantity * product.regular_price;

                this.computeTotals();
            },
            removePackageItem(index){
                if (confirm('Are you sure you want to remove this item?')) {
                    this.form.products.splice(index, 1);
                    this.computeTotals();
                }
            },
            computeTotals(){
                this.form.total = this.form.products.reduce((total, product) => total += product.total, 0);
                this.form.quantity = this.form.products.reduce((qty, product) => qty += parseInt(product.quantity), 0);
            }
        }
    })
</script>
