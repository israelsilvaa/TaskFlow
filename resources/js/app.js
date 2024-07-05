/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import "./bootstrap";
import { createApp } from "vue";

// importando e configurando vuex
import { createStore } from "vuex";

const store = createStore({
    state: {
        item: {},
        user: {},
        status: "",
        due_date: '',

        // visualizar
        assignedUsersNames: "",
        
        // criar/atualizar
        updateStatusId: 99,
        assignedUsersIds: [],
        dataEntrega: 'vazio',

        //deletar
        deletedTask: '',
    },
    mutations: {
        setDueDate(state, date) {
            state.item.due_date = date;
        },

        // atribuindo uaurios a uma task
        addUsuarioAtribuido(state, id) {
            if (!state.assignedUsersIds.includes(id)) {
                state.assignedUsersIds.push(id);
            }
        },
        removeUsuarioAtribuido(state, id) {
            state.assignedUsersIds = state.assignedUsersIds.filter(
                (userId) => userId !== id
            );
        },
    },
});


/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from "./components/ExampleComponent.vue";
import LoginComponent from "./components/Login.vue";
import RegisterComponent from "./components/Register.vue";
import HomeComponent from "./components/Home.vue";
import TasksComponent from "./components/Tasks.vue";
import CardComponent from "./components/Card.vue";
import InputComponent from "./components/InputContainer.vue";
import TableComponent from "./components/Table.vue";
import ModalComponent from "./components/Modal.vue";
import AlertComponent from "./components/Alert.vue";
import PaginateComponent from "./components/Paginate.vue";
import StatusButtonComponent from "./components/StatusButtonComponent.vue";
import StatusButtonInputComponent from "./components/StatusButtonInputComponent.vue";
import SelectUsersComponent from "./components/SelectUsers.vue";
import SelectStatusComponent from "./components/SelectStatus.vue";

app.component("example-component", ExampleComponent);
app.component("login-component", LoginComponent);
app.component("register-component", RegisterComponent);
app.component("home-component", HomeComponent);
app.component("tasks-component", TasksComponent);
app.component("card-component", CardComponent);
app.component("input-container-component", InputComponent);
app.component("table-component", TableComponent);
app.component("modal-component", ModalComponent);
app.component("alert-component", AlertComponent);
app.component("paginate-component", PaginateComponent);
app.component("status-button-component", StatusButtonComponent);
app.component("status-button-input-component", StatusButtonInputComponent);
app.component("select-users-component", SelectUsersComponent);
app.component("select-status-component", SelectStatusComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.use(store);
app.mount("#app");
