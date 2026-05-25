@if ($errors->any())
    <div style="background: #ffcccc; color: red; padding: 10px; margin-bottom: 15px;">
        <strong>Errores de validación:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div>
        <label>Título (mínimo 5 letras):</label><br>
        <input type="text" name="title" value="{{ old('title') }}" style="width: 100%;">
    </div>
    <br>

    <div>
        <label>Contenido (mínimo 50 letras):</label><br>
        <textarea name="content" rows="4" style="width: 100%;">{{ old('content') }}</textarea>
    </div>
    <br>

    <div>
        <label>Categoría:</label><br>
        <select name="category_id">
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <br>

    <div>
        <label for="attachments">Subir Archivos (Máx 5 y Máx 5MB c/u):</label><br>
        <input type="file" name="attachments[]" id="attachments" multiple>
    </div>
    <br>

    <button type="submit">Guardar Post</button>
</form>
