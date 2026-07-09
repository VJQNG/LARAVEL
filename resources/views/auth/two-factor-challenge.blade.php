<form method="POST" action="/two-factor-challenge">
    @csrf
    <div>
        <label>Código de autenticación:</label>
        <input type="text" name="code" inputmode="numeric"
               pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code">
    </div>
    <button type="submit">Verificar</button>

    {{-- Opción para usar código de recuperación --}}
    <a href="#" onclick="document.getElementById('recovery').style.display='block'">
        Usar código de recuperación
    </a>
    <div id="recovery" style="display:none">
        <input type="text" name="recovery_code">
    </div>
</form>
