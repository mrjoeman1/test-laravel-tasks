<script setup>
import {Head,Link, router} from '@inertiajs/vue3';
import {Form, useForm} from 'vee-validate';
import {string, object, number} from 'yup';
import axios from 'axios';
import {ref} from "vue";

const props = defineProps({
    task: Object,
    projects: Array,
});

const error = ref(null);

const { handleSubmit, isSubmitting, defineField, meta, errors } = useForm({
    validationSchema: object({
        name: string().max(200).required(),
        projectId: number().required(),
        priority: number().min(0).required().typeError('must be a `number`'),
    }),
});

const [name, nameAttrs] = defineField('name');
const [projectId, projectIdAttrs] = defineField('projectId');
const [priority, priorityAttrs] = defineField('priority');
const isProcessing = ref(false);

if (props.task) {
    name.value = props.task.name;
    projectId.value = props.task.project_id;
    priority.value = props.task.priority;
}

const onSubmit = handleSubmit(values => {
    error.value = null;
    isProcessing.value = true;
    const params = {
        name: values.name,
        project_id: values.projectId,
        priority: values.priority
    };
    const response = props.task
        ? axios.put('/tasks/' + props.task.id, params)
        : axios.post('/tasks', params);
    response.then(() => {
        router.visit('/tasks');
    }).catch(err => {
        error.value = err.response.data.message;
    }).finally(() => {
        isProcessing.value = false;
    });
});

</script>

<style scoped>
.edit-form {
    max-width: 400px;
    .error {
        color: #d73a49;
    }
}
</style>
<template>
    <Head title="Sign in Platform" />

    <form @submit="onSubmit">
        <div class="container mt-4 d-flex flex-column gap-3 edit-form">
            <h2 class="text-center">{{ task ? 'Edit' : 'New' }} Task</h2>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" v-model="name" v-bind="nameAttrs" class="form-control">
                <div class="error">{{ errors.name }}</div>
            </div>
            <div class="form-group">
                <label for="project">Project</label>
                <select id="project" class="form-control" v-model="projectId" v-bind="projectIdAttrs">
                    <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
                </select>
                <div class="error">{{ errors.projectId }}</div>
            </div>
            <div class="form-group">
                <label for="name">Priority</label>
                <input type="text" id="name" v-model="priority" v-bind="priorityAttrs" class="form-control">
                <div class="error">{{ errors.priority }}</div>
            </div>
            <div v-if="error" class="mt-4 error">
                {{ error }}
            </div>
            <div class="mt-4 d-flex flex-md-row gap-3 justify-content-center">
                <Link href="/tasks" class="btn btn-outline-secondary" as="button">Go Back</Link>
                <button class="btn btn-primary" :disabled="!meta.valid || isSubmitting || isProcessing">Save</button>
            </div>
        </div>
    </form>
</template>
