<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Inicio do card de busca -->
                <card-component titulo="Busca de Tasks">

                    <template v-slot:conteudo>
                        <div class="row">
                            <div class="col mb-3">
                                <input-container-component titulo="Titulo" id="inputTask" id-help="tituloHelp"
                                    texto-ajuda="Informe o Titulo da task">
                                    <input type="text" class="form-control" id="inputTask" placeholder="Backup dataBase"
                                        aria-describedby="tituloHelp" v-model="busca.title">
                                </input-container-component>
                            </div>

                            <div class="col mb-3">
                                <input-container-component titulo="Description" id="inputDescription"
                                    id-help="descriptionHelp" texto-ajuda="Informe a Descrição da task">
                                    <input type="text" class="form-control" id="inputDescription"
                                        placeholder="baixar e upar no driver" aria-describedby="descriptionHelp"
                                        v-model="busca.description">
                                </input-container-component>
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
                <!-- Fim do card de busca -->

                <!-- Inicio do card de listagem de tasks -->
                <card-component titulo="Relação de Tasks">
                    <template v-slot:conteudo>
                        <table-component :dados="tasks.data"
                            :visualizar="{ visivel: true, dataBsToggle: 'modal', dataBsTarget: '#modalTaskVisualizar' }"
                            :atualizar="{ visivel: true, dataBsToggle: 'modal', dataBsTarget: '#modalTaskAtualizar' }"
                            :remover="{ visivel: true, dataBsToggle: 'modal', dataBsTarget: '#modalTaskRemover' }"
                            :titulos="{
                                id: { titulo: 'ID', tipo: 'texto' },
                                title: { titulo: 'Titulo', tipo: 'texto' },
                                description: { titulo: 'Descrição', tipo: 'texto' },
                                status: { titulo: 'Status', tipo: 'status' },
                                assigned_users: { titulo: 'Atribuidos', tipo: 'array' },
                                user: { titulo: 'Responsavel', tipo: 'obj' },
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
                <!-- Fim do card de listagem de tasks -->
            </div>
        </div>

        <!-- Inicio Modal de criação de tasks -->
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
                        texto-ajuda="o que deve ser feito e outras observbações">
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
                        texto-ajuda="quando dever ser entregue">
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
        <!-- Fim Modal de criação de tasks -->

        <!-- Inicio Modal de visualização de tasks -->
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

                <status-button-component :status="$store.state.status"></status-button-component>

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
        <!-- Fim Modal de visualização de tasks -->

        <!-- Inicio Modal de remoção de tasks -->
        <modal-component id="modalTaskRemover" titulo="Remover task">
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
        <!-- Fim Modal de remoção de tasks -->

        <!-- Inicio Modal de atualização de tasks -->
        <modal-component id="modalTaskAtualizar" titulo="Atualizar task">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Transação realizada com sucesso"
                    :detalhes="$store.state.transacao"
                    v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro na transação" :detalhes="$store.state.transacao"
                    v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Titulo" id="atualizarNovoTitulo"
                        id-help="atualizarNovoTituloHelp" texto-ajuda="Informe o Titulo da task">
                        <input type="text" class="form-control" id="novoTitle" placeholder="Backup dataBase"
                            v-model="$store.state.item.title" aria-describedby="atualizarNovoTituloHelp">
                    </input-container-component>
                </div>

                <div class="form-group">
                    <input-container-component titulo="Descrição" id="atualizarNovoDescricao"
                        id-help="atualizarNovoDescricaoHelp" texto-ajuda="Descrição da trask">
                        <input type="text" class="form-control" id="novoDescription"
                            placeholder="baixar base da produção e salvar no driver"
                            v-model="$store.state.item.description" aria-describedby="atualizarNovoDescricaoHelp">
                    </input-container-component>
                </div>

                <label :for="id" class="form-label mb-0">Status</label>
                <div class="btn-group" role="group" aria-label="Basic mixed styles example">

                    <input type="radio" class="btn-check" name="options-outlined" id="secondary-outlined"
                        autocomplete="off" checked>
                    <label class="btn btn-outline-secondary" for="secondary-outlined">não iniciado</label>

                    <input type="radio" class="btn-check" name="options-outlined" id="danger-outlined"
                        autocomplete="off" checked>
                    <label class="btn btn-outline-danger" for="danger-outlined">Parado</label>

                    <input type="radio" class="btn-check" name="options-outlined" id="info-outlined" autocomplete="off"
                        checked>
                    <label class="btn btn-outline-info" for="info-outlined">em andamento</label>

                    <input type="radio" class="btn-check" name="options-outlined" id="success-outlined"
                        autocomplete="off" checked>
                    <label class="btn btn-outline-success" for="success-outlined">Finalizado</label>
                </div>

            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-warning" @click="atualizar()">Atualizar</button>
            </template>
        </modal-component>
        <!-- Fim Modal de atualização de tasks -->

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
            dataEntrega: '',
            transacaoStatus: '',
            transacaoDetalhes: {},
            busca: { title: '', description: '' },
        }
    },
    methods: {
        atualizar() {
            let formData = new FormData();
            formData.append('_method', 'patch')
            formData.append('title', this.$store.state.item.title)
            formData.append('description', this.$store.state.item.description)

            let url = this.urlBase + '/' + this.$store.state.item.id

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

                    filtro += chave + ":like:%" + this.busca[chave] + "%"
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

            axios.get(url)
                .then(response => {
                    this.tasks = response.data
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
                    console.log(errors);

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
    }

}
</script>
