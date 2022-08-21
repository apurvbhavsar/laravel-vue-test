<template>
    <b-card header="Posts">
        <b-button v-b-modal.modal-create class="mb-2" variant="primary"
            >Create Post</b-button
        >

        <b-modal
            v-model="create_modal_show"
            id="modal-create"
            no-close-on-backdrop
            content-class="shadow"
            title="Create Post"
        >
            <b-form
                @submit.prevent="register"
                @keydown="form.onKeydown($event)"
            >
                <b-form-group label="Title:" label-for="title">
                    <b-form-input
                        type="text"
                        id="title"
                        :class="{ 'is-invalid': form.errors.has('title') }"
                        v-model="form.title"
                    >
                    </b-form-input>
                    <has-error :form="form" field="title" />
                </b-form-group>

                <b-form-group label="Description:" label-for="description">
                    <b-form-textarea
                        id="description"
                        :class="{
                            'is-invalid': form.errors.has('description'),
                        }"
                        v-model="form.description"
                        rows="3"
                        max-rows="6"
                    >
                    </b-form-textarea>
                    <has-error :form="form" field="description" />
                </b-form-group>
            </b-form>
            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="danger"
                        class="float-right"
                        @click="create_modal_show = false"
                    >
                        Close
                    </b-button>
                    <b-button
                        type="submit"
                        variant="primary"
                        class="float-right mr-2"
                        @click="create_post()"
                    >
                        Save
                    </b-button>
                </div>
            </template>
        </b-modal>
        <b-modal
            v-model="edit_modal_show"
            id="modal-edit"
            no-close-on-backdrop
            content-class="shadow"
            title="Edit Post"
        >
            <b-form
                @submit.prevent="register"
                @keydown="editform.onKeydown($event)"
            >
                <b-form-group label="Title:" label-for="edit_title">
                    <b-form-input
                        type="text"
                        id="edit_title"
                        :class="{ 'is-invalid': editform.errors.has('title') }"
                        v-model="editform.title"
                    >
                    </b-form-input>
                    <has-error :form="editform" field="title" />
                </b-form-group>
                <b-form-group label="Description:" label-for="edit_description">
                    <b-form-textarea
                        id="edit_description"
                        :class="{
                            'is-invalid': editform.errors.has('description'),
                        }"
                        v-model="editform.description"
                        rows="3"
                        max-rows="6"
                    >
                    </b-form-textarea>
                    <has-error :form="editform" field="description" />
                </b-form-group>
            </b-form>
            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="danger"
                        class="float-right"
                        @click="edit_modal_show = false"
                    >
                        Close
                    </b-button>
                    <b-button
                        type="submit"
                        variant="primary"
                        class="float-right mr-2"
                        @click="save_post"
                    >
                        Save
                    </b-button>
                </div>
            </template>
        </b-modal>
        <b-row>
            <b-col lg="6" class="my-1">
                <b-form-group
                label="Filter"
                label-for="filter-input"
                label-cols-sm="3"
                label-align-sm="right"
                label-size="sm"
                class="mb-0"
                >
                <b-input-group size="sm">
                    <b-form-input
                    id="filter-input"
                    v-model="filter"
                    type="search"
                    placeholder="Type to Search"
                    @keyup="filterSearch()"
                    ></b-form-input>

                    <b-input-group-append>
                        <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                    </b-input-group-append>
                </b-input-group>
                </b-form-group>
            </b-col>

            <b-col lg="6" class="my-1">
                <b-form-group
                    label="Per page"
                    label-for="per-page-select"
                    label-cols-sm="6"
                    label-cols-md="4"
                    label-cols-lg="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                <b-form-select
                    id="per-page-select"
                    v-model="perPage"
                    :options="pageOptions"
                    size="sm"
                    @change="loadPosts"
                ></b-form-select>
                </b-form-group>
            </b-col>
        </b-row>
        <b-table
            striped
            hover
            show-empty
            :items="items"
            :fields="fields"
            :current-page="currentPage"
            :per-page="0"
            :filter="filter"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :sort-direction="sortDirection"
        >
            <template #cell(action)="data">
                <b-button
                    size="sm"
                    class="m-2"
                    @click="openModal(data.value)"
                    variant="info"
                >
                    Edit
                </b-button>
                <b-button
                    size="sm"
                    @click="deleteItem(data.value)"
                    variant="danger"
                >
                    Delete
                </b-button>
            </template>
            <template #cell(user)="data">
                {{ data.value.name }}
            </template>
        </b-table>
        <b-pagination
            size="md"
            :total-rows="totalItems"
            v-model="currentPage"
            :per-page="perPage"
            @change="loadPosts"
        ></b-pagination>
        <div>
            Sorting By: <b>{{ sortBy }}</b>, Sort Direction:
            <b>{{ sortDesc ? 'Descending' : 'Ascending' }}</b>
        </div>
    </b-card>
</template>

<script>
import axios from "axios";
import Form from "vform";
import _ from 'lodash';
import toast from "../mixins/message";

export default {
    middleware: "auth",
    mixins: [toast],
    data() {
        return {
            create_modal_show: false,
            edit_modal_show: false,
            sortBy: 'title',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            fields: [
                { key: 'title', label: 'Title', sortable: true, sortDirection: 'desc' },
                { key: 'user', label: 'User', sortable: true },
                { key: 'description', label: 'Description', sortable: true },
                { key: 'action', label: 'Action' }
            ],
            items: [],
            currentPage: 1,
            perPage: 10,
            totalItems: 0,
            authors: [],
            form: new Form({
                title: "",
                description: "",
            }),
            editform: new Form({
                id: 0,
                title: "",
                description: "",
            }),
            pageOptions: [5, 10, 15, { value: 100, text: "Show a lot" }],
        };
    },
    computed: {
      sortOptions() {
        // Create an options list from our fields
        return this.fields
          .filter(f => f.sortable)
          .map(f => {
            return { text: f.label, value: f.key }
          })
      }
    },
    methods: {
        filterSearch() {
            _.debounce(this.loadPosts(), 1000);
        },
        async loadPosts() {
            const { data } = await axios.get(`/api/posts?page=${this.currentPage}&per_page=${this.perPage}&sort=${this.sortBy}&direction=${this.sortDirection}&search=${this.filter}`);
            this.items = data.data;
            this.totalItems = data.meta.total;
        },
        openModal(item_id) {
            let item = this.items.find(({ id }) => id === item_id);
            this.edit_modal_show = true;
            this.editform.id = item.id;
            this.editform.title = item.title;
            this.editform.description = item.description;
        },
        async create_post() {
            const { data } = await this.form.post("/api/posts");
            if (data.data.id > 0) {
                this.$show_message(
                    `Notification`,
                    "The post was created successfully",
                    "success"
                );
                this.create_modal_show = false;
                this.loadPosts();
                this.form.title = "";
                this.form.description = "";
            } else {
                this.$show_message(
                    `Notification`,
                    "Post creating operation was failed",
                    "danger"
                );
            }
        },
        async save_post() {
            const { data } = await this.editform.post(
                "/api/posts/" + this.editform.id
            );
            if (data.data.id > 0) {
                this.$show_message(
                    `Notification`,
                    "The post was updated successfully",
                    "success"
                );
                this.edit_modal_show = false;
                this.loadPosts();
            } else {
                this.$show_message(
                    `Notification`,
                    "Post updating operation was failed",
                    "danger"
                );
            }
        },
        async deleteItem(id) {
            const { data } = await this.editform.delete("/api/posts/" + id);
            if (data.status === "success") {
                this.$show_message(
                    `Notification`,
                    "The post was deleted successfully",
                    "success"
                );
            } else {
                this.$show_message(
                    `Notification`,
                    "Post deleting operation was failed",
                    "danger"
                );
            }
            this.loadPosts();
        },
    },
    mounted() {
        this.loadPosts();
    },
};
</script>
