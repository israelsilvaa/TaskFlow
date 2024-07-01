<template>
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-10">
                <!-- Inicio do card de BUSCCA -->
                <card-component titulo="Busca de Tasks">
                    <template v-slot:conteudo>
                        <div class="row">
                            <div class="col mb-3">
                                <input-container-component titulo="Título" id="inputTask" id-help="tituloHelp"
                                    texto-ajuda="Informe o Titulo da task">
                                    <input type="text" class="form-control" id="inputTask" placeholder="Backup dataBase"
                                        aria-describedby="tituloHelp" v-model="busca.title">
                                </input-container-component>
                            </div>

                            <div class="col mb-3">
                                <input-container-component titulo="Descrição" id="inputDescription"
                                    id-help="descriptionHelp" texto-ajuda="Informe a Descrição da task">
                                    <input type="text" class="form-control" id="inputDescription"
                                        placeholder="baixar e upar no driver" aria-describedby="descriptionHelp"
                                        v-model="busca.description">
                                </input-container-component>
                            </div>

                            <div class="col mb-3">
                                <select-status-component titulo="Status" id="selectStatus" id-help="selectStatusHelp"
                                    texto-ajuda="Filtre por status">
                                    <select class="form-select m-0" aria-label="Default select example"
                                        v-model="busca.status_id">
                                        <option selected value="">Selecione um status</option>
                                        <option v-for="obj in statusList" :key="obj.id" :value="obj.id">{{ obj.name }}
                                        </option>
                                    </select>
                                </select-status-component>
                            </div>

                        </div>
                    </template>
                    <template v-slot:rodape>
                        <div class="">
                            <button type="submit" class="btn btn-primary btn-sm" @click="searchTasks()"><i
                                    class="fa-solid fa-magnifying-glass"></i> Pesquisar</button>
                        </div>
                    </template>
                </card-component>
                <!-- Fim do card de BUSCCA -->

                <!-- Inicio do card de LISTAGEM de tasks -->
                <card-component titulo="Relação de Tasks">
                    <template v-slot:conteudo>
                        <table-component :dados="tasks.data"
                            :visualizar="{ userLogged: userLogged, dataBsTarget: '#modalTaskVisualizar' }"
                            :atualizar="{ userLogged: userLogged, dataBsTarget: '#modalTaskAtualizar' }"
                            :remover="{ userLogged: userLogged, dataBsTarget: '#modalTaskRemover' }" :titulos="{
                                id: { titulo: 'ID', tipo: 'texto' },
                                title: { titulo: 'Título', tipo: 'texto' },
                                description: { titulo: 'Descrição', tipo: 'texto' },
                                status: { titulo: 'Status', tipo: 'status' },
                                assigned_users: { titulo: 'Atribuídos', tipo: 'array' },
                                user: { titulo: 'Responsável', tipo: 'obj' },
                                due_date: { titulo: 'Entrega', tipo: 'data' },
                            }"></table-component>
                    </template>
                    <template v-slot:rodape>
                        <paginate-component>
                            <li v-for="link, key in tasks.links" :key="key" class="page-item"
                                @click="paginateTasks(link)">
                                <a :class="link.active ? 'page-link active' : 'page-link'" v-html="link.label"></a>
                            </li>
                        </paginate-component>

                    </template>
                </card-component>
                <!-- Fim do card de LISTAGEM de tasks -->
            </div>
        </div>
    </div>

    <!-- Inicio Modal de CRIAÇÃO de tasks -->
    <modal-component id="modalTask" titulo="Adicionar task">
        <template v-slot:alertas>
            <alert-component tipo="success" :detalhes="this.newTaskRequest" titulo="Transação realizada com sucesso"
                v-if="this.newTaskRequest.status == 'success'"></alert-component>
            <alert-component tipo="danger" :detalhes="this.newTaskRequest" titulo="Erro na Transação"
                v-if="this.newTaskRequest.status == 'erro'"></alert-component>
        </template>

        <template v-slot:conteudo>
            <div class="form-group">
                <input-container-component titulo="Titulo" id="novaTask" id-help="novaTaskHelp"
                    texto-ajuda="Informe o Titulo da task">
                    <input type="text" class="form-control" id="novaTask" placeholder="Backup dataBase"
                        v-model="this.newTaskRequest.titulo" aria-describedby="novaTaskHelp">
                </input-container-component>
            </div>

            <div class="form-group">
                <input-container-component titulo="Descrição" id="novaDescricao" id-help="novaDescricaoHelp"
                    texto-ajuda="O que deve ser feito e outras observações">
                    <input type="text" class="form-control" id="novaDescricao"
                        placeholder="baixar base da produção e salvar no driver" v-model="this.newTaskRequest.descricao"
                        aria-describedby="novaDescricaoHelp">
                </input-container-component>
            </div>

            <div class="form-group">
                <input-container-component titulo="Atribuir usuários" id="usuariosAtribuidos"
                    id-help="usuariosAtribuidosHelp" texto-ajuda="Selecione os usuários a serem atribuídos">
                    <div class="dropdown">
                        <button class="btn border border-secondary dropdown-toggle" type="button" id="dropdownUsuarios"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-plus" style="color: #039e00;"></i> Selecionar usuários
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownUsuarios">
                            <li v-for="usuario in usuarios" :key="usuario.id">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" :id="'usuario_' + usuario.id"
                                        v-model="this.newTaskRequest.usuariosAtribuidos" :value="usuario.id">
                                    <label class="form-check-label" :for="'usuario_' + usuario.id">
                                        <!-- <i  class="fa-solid fa-shield-halved"></i>  -->
                                        <i v-if="usuario.role == 'admin'" class="fa-solid fa-shield-halved"></i>
                                        <i v-else class="fa-solid fa-user"></i>
                                        {{ usuario.name }}
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </input-container-component>
            </div>

            <div class="form-group">
                <input-container-component titulo="Data de Entrega" id="dataEntrega" id-help="dataEntregaHelp"
                    texto-ajuda="Quando dever ser entregue">
                    <input type="datetime-local" class="form-control" id="dataEntrega"
                        v-model="this.newTaskRequest.dataEntrega" aria-describedby="dataEntregaHelp">
                </input-container-component>
            </div>
        </template>

        <template v-slot:rodape>
            <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-primary" @click="saveTask()">Salvar</button>
        </template>
    </modal-component>
    <!-- Fim Modal de CRIAÇÃO de tasks -->

    <!-- Inicio Modal de VISUALIZAÇÃO de tasks -->
    <modal-component id="modalTaskVisualizar" titulo="Visualização de task">
        <template v-slot:alertas>
        </template>

        <template v-slot:conteudo>

            <input-container-component titulo="ID">
                <input type="text" class="form-control" :value="$store.state.item.id" disabled>
            </input-container-component>

            <input-container-component titulo="Titulo">
                <input type="text" class="form-control" :value="$store.state.item.title" disabled>
            </input-container-component>

            <input-container-component titulo="Descrição">
                <textarea name="" id="" cols="30" class="form-control" rows="3"
                    disabled> {{ $store.state.item.description }} </textarea>
            </input-container-component>

            <input-container-component titulo="Status">
                <status-button-component :status="$store.state.status.name"></status-button-component>
            </input-container-component>

            <input-container-component titulo="Responsável">
                <input type="text" class="form-control" :value="$store.state.user" disabled>
            </input-container-component>

            <input-container-component titulo="Usuarios atribuidos">
                <input type="text" class="form-control" :value="$store.state.assignedUsersNames" disabled>
            </input-container-component>

            <input-container-component titulo="Entrega">
                <input type="text" class="form-control" :value="$store.state.item.due_date" disabled>
            </input-container-component>
        </template>

        <template v-slot:rodape>
            <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Fechar</button>
        </template>
    </modal-component>
    <!-- Fim Modal de VISUALIZAÇÃO de tasks -->

    <!-- Inicio Modal de REMOÇÃO de tasks -->
    <modal-component id="modalTaskRemover" titulo="Remoção de task">
        <template v-slot:alertas>
            <alert-component tipo="success" :detalhes="this.newTaskRequest" titulo="Transação realizada com sucesso"
                v-if="this.newTaskRequest.status == 'success'"></alert-component>
            <alert-component tipo="danger" :detalhes="this.newTaskRequest" titulo="Erro na Transação"
                v-if="this.newTaskRequest.status == 'erro'"></alert-component>
        </template>

        <template v-slot:conteudo>
            <input-container-component titulo="ID">
                <input type="text" class="form-control" :value="$store.state.item.id" disabled>
            </input-container-component>

            <input-container-component titulo="Titulo">
                <input type="text" class="form-control" :value="$store.state.item.title" disabled>
            </input-container-component>

            <input-container-component titulo="Descrição">
                <textarea name="" id="" cols="30" class="form-control" rows="3"
                    disabled> {{ $store.state.item.description }} </textarea>
            </input-container-component>

            <input-container-component titulo="Status" v-if="isUserLoggedIn">
                <status-button-component :status="$store.state.item.status.name"></status-button-component>
            </input-container-component>
        </template>

        <template v-slot:rodape>
            <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-danger m-1" @click="deleteTask()"
                v-if="this.$store.state.deletedTask != 'deletado'">Remover</button>
        </template>
    </modal-component>
    <!-- Fim Modal de REMOÇÃO de tasks -->

    <!-- Inicio Modal de ATUALIZAÇÃO de tasks -->
    <modal-component id="modalTaskAtualizar" titulo="Atualização de task">
        <template v-slot:alertas>
            <alert-component tipo="success" :detalhes="this.newTaskRequest" titulo="Transação realizada com sucesso"
                v-if="this.newTaskRequest.status == 'success'"></alert-component>
            <alert-component tipo="danger" :detalhes="this.newTaskRequest" titulo="Erro na Transação"
                v-if="this.newTaskRequest.status == 'erro'"></alert-component>
        </template>

        <template v-slot:conteudo>
            <div class="form-group mb-2">
                <input-container-component titulo="Titulo" id="atualizarNovoTitulo" id-help="atualizarNovoTituloHelp"
                    texto-ajuda="Informe o Titulo da task">
                    <input type="text" class="form-control" id="novoTitle" placeholder="Backup dataBase"
                        v-model="$store.state.item.title" aria-describedby="atualizarNovoTituloHelp">
                </input-container-component>
            </div>

            <div class="form-group mb-2">
                <input-container-component titulo="Descrição" id="atualizarNovoDescricao"
                    id-help="atualizarNovoDescricaoHelp" texto-ajuda="O que deve ser feito e outras observações">
                    <input type="text" class="form-control" id="novoDescription"
                        placeholder="O que deve ser feito e outras observações" v-model="$store.state.item.description"
                        aria-describedby="atualizarNovoDescricaoHelp">
                </input-container-component>
            </div>

            <status-button-input-component :statusStore="$store.state.status"
                :statusList="statusList"></status-button-input-component>

            <div class="form-group mt-2 mb-2" v-if="isUserLoggedIn">
                <input-container-component titulo="Atribuir usuários"
                    texto-ajuda="Selecione os usuários a serem atribuídos">
                    <select-users-component :users="usuarios"
                        :assignedUsersIds="$store.state.assignedUsersIds"></select-users-component>
                </input-container-component>
            </div>

            <div class="form-group mb-2" v-if="isUserLoggedIn">
                <input-container-component titulo="Data de Entrega" id="dataEntrega" id-help="dataEntregaHelp"
                    texto-ajuda="quando dever ser entregue">
                    <input type="datetime-local" class="form-control" id="dataEntrega" v-model="formattedDueDate"
                        aria-describedby="dataEntregaHelp">
                </input-container-component>
            </div>
        </template>

        <template v-slot:rodape>
            <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-warning" @click="updateTask()">Atualizar</button>
        </template>
    </modal-component>
    <!-- Fim Modal de ATUALIZAÇÃO de tasks -->

</template>

<script>
import axios from 'axios';

export default {
    computed: {
        token() {
            let token = document.cookie.split(';').find(indice => {
                return indice.includes('token=')
            })
            token = token.split('=')[1]
            token = 'Bearer ' + token
            return token
        },
        formattedDueDate: {
            get() {
                // Converte o formato DD-MM-YYYY HH:MM para YYYY-MM-DDTHH:MM
                const [date, time] = this.$store.state.item.due_date.split(' ');
                const [day, month, year] = date.split('-');
                return `${year}-${month}-${day}T${time}`;
            },
            set(value) {
                // Converte o formato YYYY-MM-DDTHH:MM para YYYY-MM-DD HH:MM:SS
                const [date, time] = value.split('T');
                const [year, month, day] = date.split('-');
                const formattedDate = `${year}-${month}-${day} ${time}:00`;
                this.$store.state.dataEntrega = formattedDate;
            }
        },
        isUserLoggedIn() {
            return this.$store.state.item && this.$store.state.item.user && this.userLogged.id == this.$store.state.item.user.id;
        }
    },
    data() {
        return {
            // busca
            urlBase: 'http://localhost:8000/api/v1/task',
            urlPaginacao: '',
            urlFiltro: '',
            busca: { title: '', description: '', status_id: '' },

            // visualizar 
            tasks: { data: [] }, // definido como vazio, aguardar a requisição de tasks terminar: loadTaskList()
            statusList: {},
            usuarios: { data: [] },

            // atualizar/criar
            newTaskRequest: {
                status: "",
                mensagem: "",
                titulo: "",
                descricao: "",
                usuariosAtribuidos: "",
                dataEntrega: "",
            },

            transacaoStatus: '',
            transacaoDetalhes: {},

            // permissão botão: atualizar/remover
            userLogged: '',
        }
    },
    methods: {
        saveTask() {
            let formData = new FormData();
            formData.append('title', this.newTaskRequest.titulo);
            formData.append('description', this.newTaskRequest.descricao);
            formData.append('usuariosAtribuidos', JSON.stringify(this.usuariosAtribuidos)); // Converte para string JSON

            // Converte a data de entrega para o formato YYYY-MM-DD HH:MM:SS
            let dataEntrega = new Date(this.newTaskRequest.dataEntrega);
            let year = dataEntrega.getFullYear();
            let month = (dataEntrega.getMonth() + 1).toString().padStart(2, '0');
            let day = dataEntrega.getDate().toString().padStart(2, '0');
            let hours = dataEntrega.getHours().toString().padStart(2, '0');
            let minutes = dataEntrega.getMinutes().toString().padStart(2, '0');
            let seconds = '00'; // Definido como '00' se não estiver disponível

            let formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            formData.append('due_date', formattedDate);

            // Recupera a resposta/erros de forma assíncrona
            axios.post(this.urlBase, formData)
                .then(response => {
                    this.newTaskRequest.status = 'success'
                    this.newTaskRequest.mensagem = response.data.success.detail
                    this.newTaskRequest.dados = ''
                    setTimeout(() => {
                        this.newTaskRequest = {}
                    }, 2500);
                })
                .catch(errors => {
                    this.newTaskRequest.status = 'erro'
                    this.newTaskRequest.mensagem = errors.response.data.error.detail
                    this.newTaskRequest.dados = errors.response.data.error.detail
                    setTimeout(() => {
                        this.newTaskRequest.status = ''
                    }, 2500);
                });
            this.loadTaskList()
        },
        updateTask() {
            let url = this.urlBase + '/' + this.$store.state.item.id
            let formData = new FormData();

            formData.append('_method', 'patch')
            formData.append('title', this.$store.state.item.title)
            formData.append('description', this.$store.state.item.description)

            if (this.$store.state.updateStatusId == 99) {
                formData.append('status_id', this.$store.state.status.id)
            } else {
                formData.append('status_id', this.$store.state.updateStatusId)
            }
            formData.append('usuariosAtribuidos', this.$store.state.assignedUsersIds); // Converte para string JSON

            let dataEntregaString = this.$store.state.dataEntrega || this.$store.state.item.due_date;

            if (dataEntregaString) {
                // Converte a data de entrega para o formato YYYY-MM-DD HH:MM:SS
                let dataEntrega = new Date(dataEntregaString);
                let year = dataEntrega.getFullYear();
                let month = (dataEntrega.getMonth() + 1).toString().padStart(2, '0');
                let day = dataEntrega.getDate().toString().padStart(2, '0');
                let hours = dataEntrega.getHours().toString().padStart(2, '0');
                let minutes = dataEntrega.getMinutes().toString().padStart(2, '0');
                let seconds = '00'; // Definido como '00' se não estiver disponível
                let formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

                formData.append('due_date', formattedDate);
            }


            axios.post(url, formData)
                .then(response => {
                    this.newTaskRequest.status = 'success'
                    this.newTaskRequest.mensagem = response.data.success.detail
                    this.newTaskRequest.dados = ''
                    setTimeout(() => {
                        this.newTaskRequest = {}
                    }, 3000);
                })
                .catch(errors => {
                    this.newTaskRequest.status = 'erro'
                    this.newTaskRequest.mensagem = errors.response.data.error.detail
                    this.newTaskRequest.dados = errors.response.data.error.detail
                    setTimeout(() => {
                        this.newTaskRequest.status = ''
                    }, 3000);
                });

            this.loadTaskList()

        },
        deleteTask() {
            let confirmacao = confirm('Tem certeza que deseja remover essa task?')
            if (!confirmacao) return false;

            let formData = new FormData();
            formData.append('_method', 'delete')

            let url = this.urlBase + '/' + this.$store.state.item.id

            axios.post(url, formData)
                .then(response => {
                    this.newTaskRequest.status = 'success'
                    this.newTaskRequest.mensagem = response.data.success.detail
                    this.newTaskRequest.dados = ''

                    setTimeout(() => {
                        this.newTaskRequest.status = ''
                    }, 2500);
                    this.$store.state.deletedTask = 'deletado';
                })
                .catch(errors => {
                    this.newTaskRequest.status = 'erro'
                    this.newTaskRequest.mensagem = errors.response.data.error.detail
                    this.newTaskRequest.dados = errors.response.data.error.detail
                    setTimeout(() => {
                        this.newTaskRequest.status = ''
                    }, 2500);
                });
            this.loadTaskList()
        },
        searchTasks() {
            let filtro = ''
            for (let chave in this.busca) {
                if (this.busca[chave]) {
                    if (filtro != '') {
                        filtro += ";"
                    }

                    filtro += chave + ":like:" + this.busca[chave]
                }
            }
            if (filtro != '') {
                this.urlPaginacao = 'page=1'
                this.urlFiltro = '&filtro=' + filtro
            } else {
                this.urlFiltro = ''
            }
            this.loadTaskList() // carrega lista com filtros de pesquisa atualizados
        },
        paginateTasks(link) {
            if (link.url) {
                // this.urlBase = link.url // ajustar parametro de consulta com parametro da pagina
                this.urlPaginacao = link.url.split('?')[1]
                this.loadTaskList() // requisita dados para API, da pagina desejada 
            }
        },
        loadTaskList() {
            let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro;
            axios.get(url)
                .then(response => {
                    this.tasks = response.data
                })
                .catch(errors => {
                });

        },
        loadLoggedInUser() {
            let url = 'http://127.0.0.1:8000/api/me';
            axios.post(url)
                .then(response => {
                    this.userLogged = response.data.success.detail.User
                })
                .catch(errors => {
                });
        },
        loadUsers() {
            let url = 'http://localhost:8000/api/v1/users'

            axios.get(url)
                .then(response => {
                    this.usuarios = response.data.success.detail
                })
                .catch(errors => {
                });
        },
        loadStatuses() {
            let url = 'http://localhost:8000/api/v1/status'

            axios.get(url)
                .then(response => {
                    this.statusList = response.data.success.detail
                })
                .catch(errors => {
                });
        },

    },
    mounted() {
        this.loadTaskList()
        this.loadUsers()
        this.loadStatuses()
        this.loadLoggedInUser()
    }

}
</script>
