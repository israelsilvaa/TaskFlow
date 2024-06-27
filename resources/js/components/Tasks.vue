<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Inicio do card de busca -->
                <div class="card">
                    <div class="card-header">Busca de Tasks</div>

                    <div class="card-body">
                        <div class="row">

                            <div class="col mb-3">
                                <input-container-component titulo="ID" id="inputId" id-help="idHelp"
                                    texto-ajuda="Opcional. Informe o ID da task">
                                    <input type="number" class="form-control" id="inputId" placeholder="55"
                                        aria-describedby="idHelp">
                                </input-container-component>
                            </div>

                            <div class="col mb-3">
                                <input-container-component titulo="Titulo" id="inputTask" id-help="tituloHelp"
                                    texto-ajuda="Informe o Titulo da task">
                                    <input type="text" class="form-control" id="inputTask" placeholder="Backup dataBase"
                                        aria-describedby="tituloHelp">
                                </input-container-component>
                            </div>

                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                    </div>
                </div>
                <!-- Fim do card de busca -->

                <!-- Inicio do card de tasks -->
                <div class="card mt-3">
                    <div class="card-header">Relação de Tasks</div>

                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td colspan="2">Larry the Bird</td>
                                    <td>@twitter</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm">Adicionar</button>
                    </div>
                </div>
                <!-- Fim do card de tasks -->
            </div>
        </div>
    </div>
</template>

<script>
export default {


}
</script>
