<div class="p-3">
    <h5>Categorías</h5>
    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="catRemeras" value="remeras">
        <label class="form-check-label" for="catRemeras">Remeras</label>
    </div>
    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="catPantalones" value="pantalones">
        <label class="form-check-label" for="catPantalones">Pantalones</label>
    </div>
    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="catCamperas" value="camperas">
        <label class="form-check-label" for="catCamperas">Camperas</label>
    </div>
    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="catVestidos" value="vestidos">
        <label class="form-check-label" for="catVestidos">Vestidos</label>
    </div>
    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="catCalzado" value="calzado">
        <label class="form-check-label" for="catCalzado">Calzado</label>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="catAccesorios" value="accesorios">
        <label class="form-check-label" for="catAccesorios">Accesorios</label>
    </div>

    <hr class="my-3">

    <h5>Ordenar por precio</h5>
    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="ordenPrecio" id="ordenAsc" value="asc">
        <label class="form-check-label" for="ordenAsc">Menor a mayor</label>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="radio" name="ordenPrecio" id="ordenDesc" value="desc">
        <label class="form-check-label" for="ordenDesc">Mayor a menor</label>
    </div>

    <hr class="my-3">

    <h5>Rango de precio</h5>
    <div class="mb-3">
        <label for="precioMin" class="form-label">Precio mínimo</label>
        <input type="number" class="form-control" id="precioMin" min="0" placeholder="$ 0">
    </div>
    <div class="mb-3">
        <label for="precioMax" class="form-label">Precio máximo</label>
        <input type="number" class="form-control" id="precioMax" min="0" placeholder="$ 50000">
    </div>

    <button type="button" class="btn btn-success w-100" id="btnAplicarFiltros">Aplicar</button>
</div>

    