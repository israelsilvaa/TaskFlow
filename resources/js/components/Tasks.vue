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

                            <!-- <div class="col mb-3">
                                <input-container-component titulo="User" id="inputUser" id-help="userHelp"
                                    texto-ajuda="Usuário relacionado a task">
                                    <input type="text" class="form-control" id="inputUser" placeholder="israel"
                                        aria-describedby="userHelp" v-model="busca.assigned_user_names">
                                </input-container-component>
                            </div> -->

                        </div>
                    </template>
                    <template v-slot:rodape>
                        <div class="">
                            <button type="submit" class="btn btn-primary btn-sm" @click="pesquisar()">Pesquisar</button>
                        </div>
                    </template>

                </card-component>
                <!-- Fim do card de busca -->

                <!-- Inicio do card de listagem de tasks -->
                <card-component titulo="Relação de Tasks">
                    <template v-slot:conteudo>
                        <table-component :dados="tasks.data" 
                            :concluir="true"
                            :visualizar="{ visivel: true, dataBsToggle: 'modal', dataBsTarget: '#modalTaskVisualizar' }"
                            :atualizar="{ visivel: true, dataBsToggle: 'modal', dataBsTarget: '#modalTaskAtualizar' }"
                            :remover="{ visivel: true, dataBsToggle: 'modal', dataBsTarget: '#modalTaskRemover' }"
                            :titulos="['id', 'title', 'description', 'status', 'created_at']"></table-component>
                    </template>
                    <template v-slot:rodape>
                        <paginate-component>
                            <li v-for="link, key in tasks.links" :key="key" class="page-item" @click="paginacao(link)">
                                <a :class="link.active ? 'page-link active' : 'page-link'" v-html="link.label"></a>
                            </li>
                        </paginate-component>
                        <button type="button" class="btn btn-primary btn-sm m-3" data-bs-toggle="modal"
                            data-bs-target="#modalTask">Adicionar</button>
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
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca"
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
                        texto-ajuda="Descrição da trask">
                        <input type="text" class="form-control" id="novaDescricao"
                            placeholder="baixar base da produção e salvar no driver" v-model="descricao"
                            aria-describedby="novaDescricaoHelp">
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
        <modal-component id="modalTaskVisualizar" titulo="Visualizar task">
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
                    <input type="text" class="form-control" :value="$store.state.item.status" disabled>
                </input-container-component>

                <input-container-component titulo="Responsável">
                    <input type="text" class="form-control" :value="$store.state.responsavel.name" disabled>
                </input-container-component>

                <input-container-component titulo="Usuarios relacionados">
                    <input type="text" class="form-control" :value="$store.state.relacionados" disabled>
                </input-container-component>

                <input-container-component titulo="Created_at">
                    <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
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
                    <input-container-component titulo="Titulo" id="atualizarNovoTitulo" id-help="atualizarNovoTituloHelp"
                        texto-ajuda="Informe o Titulo da task">
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

            console.log(this.$store.state.item)
            let config = {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }
            let url = this.urlBase + '/' + this.$store.state.item.id

            axios.post(url, formData, config)
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
            let config = {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }
            let url = this.urlBase + '/' + this.$store.state.item.id

            axios.post(url, formData, config)
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

            let config = {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }
            axios.get(url, config)
                .then(response => {
                    this.tasks = response.data
                })
                .catch(errors => {
                });

        },
        salvar() {
            let formData = new FormData();
            formData.append('title', this.titulo)
            formData.append('description', this.descricao)
            let config = {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }

            // recupera a resposta/erros de forma assincrona
            axios.post(this.urlBase, formData, config)
                .then(response => {
                    this.transacaoStatus = 'adicionado'
                    this.transacaoDetalhes = {
                        mensagem: 'ID do registro: ' + response.data.success.detail.id
                    }
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
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
    }

}
</script>
