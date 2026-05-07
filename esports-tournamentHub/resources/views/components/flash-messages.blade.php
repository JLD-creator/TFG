@if (session('success'))
    <div class="alert alert-success alert-hub" role="alert">
        <div class="fw-semibold mb-1">Operacion completada</div>
        <p class="mb-0">{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-hub" role="alert">
        <div class="fw-semibold mb-1">No se pudo completar la operacion</div>
        <p class="mb-0">{{ session('error') }}</p>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-hub" role="alert">
        <div class="fw-semibold mb-2">Revisa los datos introducidos</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
