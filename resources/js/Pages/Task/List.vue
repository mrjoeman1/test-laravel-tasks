<script setup>
import {Head, router, Link} from '@inertiajs/vue3';
import {ref, watch} from "vue";
import axios from "axios";
import { VueDraggableNext as draggable } from 'vue-draggable-next';

const props = defineProps({
    projects: Array,
    tasks: Array,
    projectId: Number,
});

const selectedProjectId = ref(props.projectId);

watch(selectedProjectId, () => reload());

const reload = () => {
    router.reload({ only: ['tasks', 'projectId'], data: { project_id: selectedProjectId.value } });
};

const editItem = (task) => {
    router.visit('/tasks/' + task.id + '/edit');
};

const deleteItem = (task) => {
    axios.delete('/tasks/' + task.id).then(() => reload());
};

const dropped = (evt) => {
    const taskId = props.tasks[evt.oldIndex].id;
    const newPriority = props.tasks[evt.newIndex].priority;
    axios.patch('/tasks/' + taskId, { priority: newPriority }).then(() => reload());
};
</script>


<style scoped>
.task-row {
    background: #fff;
    border-bottom: 1px solid #afafaf;
    padding: 0.5em;
    .dnd-handler {
        cursor: move;
    }
}
</style>

<template>
    <Head title="Tasks Manager" />
    <div class="container d-flex flex-column gap-3 my-4">
        <h2>
            Tasks Manager
        </h2>
        <div class="d-flex flex-column flex-md-row gap-3 p-3 justify-content-between align-items-end">
            <Link href="/tasks/create" class="btn btn-primary" as="button">
                <span class="material-icons align-bottom">add</span> Add Task
            </Link>
            <div class="form-group">
                <label for="project" class="fw-bold">Project</label>
                <select id="project" class="form-control" v-model="selectedProjectId">
                    <option :value="''">Not specified</option>
                    <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
                </select>
            </div>
        </div>
        <div class="card shadow border-r p-3">
            <div class="container tasks">
                <div class="row mb-2 task-row fw-bold">
                    <div class="col-md-3">Name</div>
                    <div class="col-md-3">Project</div>
                    <div class="col-md-1">Priority</div>
                    <div class="col-md-3 text-nowrap">Created At /<br/>Updated At</div>
                    <div class="col-md-2 text-nowrap"></div>
                </div>
                <draggable @end="dropped($event)" class="dropzone">
                    <div v-for="task in tasks" :key="task.id" class="row mb-2 task-row">
                        <div class="col-md-3"><span class="material-icons align-bottom dnd-handler">drag_indicator</span> {{ task.name }}</div>
                        <div class="col-md-3">{{ task.project?.name }}</div>
                        <div class="col-md-1">{{ task.priority }}</div>
                        <div class="col-md-3">{{ $filters.formatDate(task.created_at) }} /<br/>{{ $filters.formatDate(task.updated_at) }}</div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-primary" @click="editItem(task)">
                                    <span class="material-icons align-bottom">edit</span>
                                </button>
                                <button class="btn btn-danger" @click="deleteItem(task)">
                                    <span class="material-icons align-bottom">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </draggable>
            </div>
        </div>
    </div>
</template>
