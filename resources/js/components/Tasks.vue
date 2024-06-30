<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
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
                            <button type="submit" class="btn btn-primary btn-sm" @click="pesquisar()"><i
                                    class="fa-solid fa-magnifying-glass"></i> Pesquisar</button>
                        </div>
                    </template>
                </card-component>
                <!-- Fim do card de BUSCCA -->

                <!-- Inicio do card de LISTAGEM de tasks -->
                <card-component titulo="Relação de Tasks">
                    <template v-slot:conteudo>
                        <table-component :dados="tasks.data"
                            :visualizar="{ userRole: userRole, dataBsTarget: '#modalTaskVisualizar' }"
                            :atualizar="{ userRole: userRole, dataBsTarget: '#modalTaskAtualizar' }"
                            :remover="{ userRole: userRole, dataBsTarget: '#modalTaskRemover' }" :titulos="{
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
                            <li v-for="link, key in tasks.links" :key="key" class="page-item" @click="paginacao(link)">
                                <a :class="link.active ? 'page-link active' : 'page-link'" v-html="link.label"></a>
                            </li>
                        </paginate-component>

                    </template>
                </card-component>
                <!-- Fim do card de LISTAGEM de tasks -->
            </div>
        </div>

        <!-- Inicio Modal de CRIAÇÃO de tasks -->
        <modal-component id="modalTask" titulo="Adicionar task">
            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso"
                    v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a task"
                    v-if="transacaoStatus == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Titulo" id="novaTask" id-help="novaTaskHelp"
                        texto-ajuda="Informe o Titulo da task">
                        <input type="text" class="form-control" id="novaTask" placeholder="Backup dataBase"
                            v-model="titulo" aria-describedby="novaTaskHelp">
                    </input-container-component>
                </div>

                <div class="form-group">
                    <input-container-component titulo="Descrição" id="novaDescricao" id-help="novaDescricaoHelp"
                        texto-ajuda="O que deve ser feito e outras observações">
                        <input type="text" class="form-control" id="novaDescricao"
                            placeholder="baixar base da produção e salvar no driver" v-model="descricao"
                            aria-describedby="novaDescricaoHelp">
                    </input-container-component>
                </div>

                <div class="form-group">
                    <input-container-component titulo="Atribuir usuários" id="usuariosAtribuidos"
                        id-help="usuariosAtribuidosHelp" texto-ajuda="Selecione os usuários a serem atribuídos">
                        <div class="dropdown">
                            <button class="btn border border-secondary dropdown-toggle" type="button"
                                id="dropdownUsuarios" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-user-plus" style="color: #039e00;"></i> Selecionar usuários
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownUsuarios">
                                <li v-for="usuario in usuarios" :key="usuario.id">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" :id="'usuario_' + usuario.id"
                                            v-model="usuariosAtribuidos" :value="usuario.id">
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
                        <input type="datetime-local" class="form-control" id="dataEntrega" v-model="dataEntrega"
                            aria-describedby="dataEntregaHelp">
                    </input-container-component>
                </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
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
                    <input type="text" class="form-control" :value="$store.state.relacionados" disabled>
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
                <alert-component tipo="success" titulo="Transação realizada com sucesso"
                    :detalhes="$store.state.transacao"
                    v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro na transação" :detalhes="$store.state.transacao"
                    v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
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
                    <input type="text" class="form-control" :value="$store.state.item.status" disabled>
                </input-container-component>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger m-1" @click="remover()"
                    v-if="$store.state.transacao.status != 'sucesso'">Remover</button>
            </template>
        </modal-component>
        <!-- Fim Modal de REMOÇÃO de tasks -->

        <!-- Inicio Modal de ATUALIZAÇÃO de tasks -->
        <modal-component id="modalTaskAtualizar" titulo="Atualização de task">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Transação realizada com sucesso"
                    :detalhes="$store.state.transacao"
                    v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro na transação" :detalhes="$store.state.transacao"
                    v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group mb-2">
                    <input-container-component titulo="Titulo" id="atualizarNovoTitulo"
                        id-help="atualizarNovoTituloHelp" texto-ajuda="Informe o Titulo da task">
                        <input type="text" class="form-control" id="novoTitle" placeholder="Backup dataBase"
                            v-model="$store.state.item.title" aria-describedby="atualizarNovoTituloHelp">
                    </input-container-component>
                </div>

                <div class="form-group mb-2">
                    <input-container-component titulo="Descrição" id="atualizarNovoDescricao"
                        id-help="atualizarNovoDescricaoHelp" texto-ajuda="O que deve ser feito e outras observações">
                        <input type="text" class="form-control" id="novoDescription"
                            placeholder="O que deve ser feito e outras observações"
                            v-model="$store.state.item.description" aria-describedby="atualizarNovoDescricaoHelp">
                    </input-container-component>
                </div>

                <status-button-input-component :status="$store.state.status"
                    :statusList="statusList"></status-button-input-component>

                <div class="form-group mt-2 mb-2" v-if="userRole == 'admin'">
                    <input-container-component titulo="Atribuir usuários"
                        texto-ajuda="Selecione os usuários a serem atribuídos">
                        <select-users-component :users="usuarios"
                            :assignedUsersIds="$store.state.assignedUsersIds"></select-users-component>
                    </input-container-component>
                </div>

                <div class="form-group mb-2" v-if="userRole == 'admin'">
                    <input-container-component titulo="Data de Entrega" id="dataEntrega" id-help="dataEntregaHelp"
                        texto-ajuda="quando dever ser entregue">
                        <input type="datetime-local" class="form-control" id="dataEntrega" v-model="formattedDueDate"
                            aria-describedby="dataEntregaHelp">
                    </input-container-component>
                </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-warning" @click="atualizar()">Atualizar</button>
            </template>
        </modal-component>
        <!-- Fim Modal de ATUALIZAÇÃO de tasks -->

    </div>
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
                const dueDate = this.$store.state.item.due_date;
                if (!dueDate) return ''; // Retorna string vazia se due_date for undefined

                const [date, time] = dueDate.split(' ');
                const [day, month, year] = date.split('-');
                return `${year}-${month}-${day}T${time}`;
            },
            set(value) {
                if (!value) {
                    this.$store.commit('setDueDate', '');
                    return;
                }

                const [date, time] = value.split('T');
                const [year, month, day] = date.split('-');
                this.$store.commit('setDueDate', `${day}-${month}-${year} ${time}`);
            }
        }
    },
    data() {
        return {
            urlBase: 'http://localhost:8000/api/v1/task',
            urlPaginacao: '',
            urlFiltro: '',
            tasks: { data: [] }, // definido como vazio, aguardar a requisição de tasks terminar: carregarListaTasks()
            titulo: '',
            descricao: '',
            usuariosAtribuidos: [],
            usuarios: { data: [] },
            statusList: {},
            dataEntrega: '',
            transacaoStatus: '',
            transacaoDetalhes: {},
            busca: { title: '', description: '', status_id: '' },
            userRole: '',
        }
    },
    methods: {
        atualizar() {
            let url = this.urlBase + '/' + this.$store.state.item.id
            let formData = new FormData();

            formData.append('_method', 'patch')
            formData.append('title', this.$store.state.item.title)
            formData.append('description', this.$store.state.item.description)
            formData.append('status_id', this.$store.state.updateStatusId)
            formData.append('usuariosAtribuidos', this.$store.state.assignedUsersIds); // Converte para string JSON            
            formData.append('due_date', this.formattedDueDate)

            axios.post(url, formData)
                .then(response => {
                    this.$store.state.transacao.status = 'sucesso'
                    this.$store.state.transacao.mensagem = response.data.success.detail
                    this.carregarListaTasks()
                })
                .catch(errors => {
                    this.$store.state.transacao.status = 'erro'
                    this.$store.state.transacao.mensagem = errors.response.data.error.detail
                });
        },
        remover() {
            let confirmacao = confirm('Tem certeza que deseja remover essa task?')
            if (!confirmacao) return false;

            let formData = new FormData();
            formData.append('_method', 'delete')

            let url = this.urlBase + '/' + this.$store.state.item.id

            axios.post(url, formData)
                .then(response => {
                    this.$store.state.transacao.status = 'sucesso'
                    this.$store.state.transacao.mensagem = response.data.success.detail
                })
                .catch(errors => {
                    this.$store.state.transacao.status = 'erro'
                    this.$store.state.transacao.mensagem = errors.response.data.error.detail
                });
            this.carregarListaTasks()
        },
        pesquisar() {
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
            this.carregarListaTasks() // carrega lista com filtros de pesquisa atualizados
        },
        paginacao(link) {
            if (link.url) {
                // this.urlBase = link.url // ajustar parametro de consulta com parametro da pagina
                this.urlPaginacao = link.url.split('?')[1]
                this.carregarListaTasks() // requisita dados para API, da pagina desejada 
            }
        },
        carregarListaTasks() {
            let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro;
            console.log('Pesquisa: ', url)
            axios.get(url)
                .then(response => {
                    this.tasks = response.data
                })
                .catch(errors => {
                });

        },
        carregarUsuarioLogado() {
            let url = 'http://127.0.0.1:8000/api/me';
            axios.post(url)
                .then(response => {
                    this.userRole = response.data.success.detail.User.role
                })
                .catch(errors => {
                });
        },
        carregarUsers() {
            let url = 'http://localhost:8000/api/v1/users'

            axios.get(url)
                .then(response => {
                    this.usuarios = response.data.success.detail
                })
                .catch(errors => {
                });
        },
        carregarStatus() {
            let url = 'http://localhost:8000/api/v1/status'

            axios.get(url)
                .then(response => {
                    this.statusList = response.data.success.detail
                })
                .catch(errors => {
                });
        },
        salvar() {
            let formData = new FormData();
            formData.append('title', this.titulo);
            formData.append('description', this.descricao);
            formData.append('usuariosAtribuidos', JSON.stringify(this.usuariosAtribuidos)); // Converte para string JSON
            // Converte para formato `YYYY-MM-DD HH:MM:SS`
            let dataEntrega = new Date(this.dataEntrega).toISOString().slice(0, 19).replace('T', ' ');
            formData.append('due_date', dataEntrega);

            // Recupera a resposta/erros de forma assíncrona
            axios.post(this.urlBase, formData)
                .then(response => {
                    this.transacaoStatus = 'adicionado';
                    this.transacaoDetalhes = {
                        mensagem: 'ID do registro: ' + response.data.success.detail.id
                    };
                })
                .catch(errors => {

                    this.transacaoStatus = 'erro';
                    if (errors.response && errors.response.data) {
                        if (errors.response.data.error) {
                            // Caso 1: Formato com 'error'
                            this.transacaoDetalhes = {
                                mensagem: errors.response.data.error.title,
                                dados: errors.response.data.error.detail
                            };
                        } else if (errors.response.data.message) {
                            // Caso 2: Formato com 'message'
                            this.transacaoDetalhes = {
                                mensagem: errors.response.data.message,
                                // dados: errors.response.data.errors
                            };
                        }
                    }
                });

            this.carregarListaTasks()
        }
    },
    mounted() {
        this.carregarListaTasks()
        this.carregarUsers()
        this.carregarStatus()
        this.carregarUsuarioLogado()
    }

}
</script>
