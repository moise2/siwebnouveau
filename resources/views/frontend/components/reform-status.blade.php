<div id="etatreformes" class="row border-top border-danger rounded p-4">
    <h1 class="mb-3">État des Réformes</h1>
    <p class="text-muted" style="font-family: Poppins, sans-serif; font-size: 1rem;">
        Veuillez sélectionner l'axe (ex: feuille de route gouvernementale) et l’exercice (année).
    </p>

    <!-- Formulaire de sélection -->
    <form class="reform-form mt-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="axes" class="form-label">Axe</label>
                <select id="axes" name="axes" class="form-select" required aria-label="Sélectionnez un axe">
                    <option value="" disabled selected>Sélectionnez un axe</option>
                    
                        <option value=""></option>
                   
                </select>
            </div>
            <div class="col-md-6">
                <label for="exercice" class="form-label">Exercice</label>
                <select id="exercice" name="exercice" class="form-select" required aria-label="Sélectionnez une année">
                    <option value="" disabled selected>Sélectionnez une année</option>
                    
                        <option value=""></option>
                    
                </select>
            </div>
        </div>

        <!-- Boutons de validation et annulation -->
        <div class="d-flex justify-content-end mt-4">
            <button type="reset" id="annuler" class="btn btn-secondary me-2">Annuler</button>
            <button type="button" id="valider" class="btn btn-primary">Valider</button>
        </div>
    </form>

    <!-- Section d'informations et graphiques -->
    <div id="infoContainer" class="info-container mt-5 d-none">
        <h2 class="mt-3">Détails sur l'Axe et l'Exercice Sélectionnés</h2>
        <div id="selectedInfo" class="mb-4"></div>

        <!-- Graphiques -->
        <div class="row">
            <div class="col-lg-4 col-md-12">
                <div class="card card-body mb-3" style="height: 300px;">
                    <h3>Répartition des Réformes</h3>
                    <canvas id="pieChart" style="height: 100%;"></canvas>
                </div>
            </div>
            <div class="col-lg-8 col-md-12">
                <div class="card card-body mb-3" style="height: 300px;">
                    <h3>Évolution des Réformes au Cours de l'Année</h3>
                    <canvas id="lineChart" style="height: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Tableau des réformes -->
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th scope="col">Nom de la Réforme</th>
                        <th scope="col">Objectifs de la réforme</th>
                        <th scope="col">Statut</th>
                        <th scope="col">Axe Strategique</th>
                        <th scope="col">Institution Responsable</th>
                        <th scope="col">Année Fin</th>
                    </tr>
                </thead>
                <tbody>
                    
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                
            </div>
        </div>
    </div>

    <!-- Spinner de chargement -->
    <div id="loadingSpinner" class="text-center d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Chargement...</span>
        </div>
    </div>

    <!-- Message d'alerte -->
    <div id="alertMessage" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
        <strong>Erreur!</strong> <span id="alertText"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>


